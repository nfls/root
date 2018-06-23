<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\User\Chat;
use App\Entity\User\User;
use App\Model\Permission;
use App\Service\Notification\NotificationService;
use GuzzleHttp\Client;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user/register", name="register", methods="POST")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator)
    {
        if (!$this->verifyCaptcha($request->request->get("captcha")))
            return $this->response()->response($translator->trans("incorrect-captcha"), Response::HTTP_UNAUTHORIZED);
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $target = $this->getTarget($request);
        if (is_null($target))
            return $this->response()->response($translator->trans("incorrect-phone-or-email"), Response::HTTP_UNAUTHORIZED);
        $username = $request->request->get("username");
        if ($this->verifyUsername($username)) {
            $user->setUsername($username);
        } else {
            return $this->response()->response($translator->trans("illegal-username"), Response::HTTP_UNAUTHORIZED);
        }
        $password = $request->request->get("password");
        if ($this->verifyPassword($user, $password)) {
            $user->setPassword($passwordEncoder->encodePassword($user, $password));
        } else {
            return $this->response()->response($translator->trans("illegal-password"), Response::HTTP_UNAUTHORIZED);
        }
        if ($this->notification()->verify($target, $request->request->get("code"), NotificationService::ACTION_REGISTERING)) {
            if ($target instanceof PhoneNumber) {
                $util = PhoneNumberUtil::getInstance();
                $user->setPhone($util->format($target, PhoneNumberFormat::E164));
            } else {
                $user->setEmail($target);
            }
        } else {
            return $this->response()->response($translator->trans("incorrect-code"), Response::HTTP_UNAUTHORIZED);
        }
        $em->persist($user);
        $em->flush();
        $user = $em->getRepository(User::class)->findByUsername($username);
        $this->writeLog("UserCreated", null, $user);
        $this->getDefaultAvatar($username, $user->getId());
        return $this->response()->response(null);
    }

    private function getTarget(Request $request)
    {
        $phone = intval($request->request->get("phone"));
        if ($phone > 0) {
            $country = $request->request->get("country");
            $util = PhoneNumberUtil::getInstance();
            try {
                $phoneObject = $util->parse($phone, $country);
                return $phoneObject;
            } catch (NumberParseException $e) {
                return null;
            }
        } else {
            $email = $request->request->get("email");
            return $email;
        }
    }

    private function verifyUsername($username)
    {
        $re = '/[A-Za-z0-9_\-\x{0800}-\x{9fa5}]{3,16}/u';
        if (preg_match($re, $username) && !is_numeric($username[0])) {
            return true;
        } else {
            return false;
        }

    }

    private function verifyPassword(User $user, $password)
    {
        //Strength
        $re = '/^((?!.*[\s])(?=\S*?[a-zA-Z])(?=\S*?[0-9]).{6,})$/';
        if (!preg_match($re, $password))
            return false;
        //Email
        if (null !== $user->getEmail() && preg_match('/' . $user->getEmail() . '/', $password))
            return false;
        //Username
        if (preg_match('/' . $user->getUsername() . '/', $password))
            return false;

        return true;
    }

    private function getDefaultAvatar($username, $id)
    {
        file_put_contents($this->get('kernel')->getRootDir() . "/../public/avatar/" . strval($id) . ".png", fopen('http://identicon.relucks.org/' . md5($username) . '?size=200', 'r'));
    }

    /**
     * @Route("/user/login", name="login")
     */
    public function login(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator)
    {
        $session = $request->getSession();
        if (!$session)
            $session = new Session();
        $session->start();
        $user = $this->getDoctrine()->getRepository(User::class)->search($request->request->get("username"));
        if (null === $user)
            return $this->response()->response($translator->trans("incorrect-password"), Response::HTTP_UNAUTHORIZED);
        if ($passwordEncoder->isPasswordValid($user, $request->request->get("password", $user->getSalt()))) {
            $session->set("user_token", $user->getToken());

            if ($request->request->get("remember") == "true") {
                $response = $this->response()->response(null);
                $time = new \DateTime();
                $time->add(new \DateInterval("P1M"));
                $response->headers->setCookie(new Cookie("remember_token", $user->getToken(), $time, "/", null, false, true));
                return $response;
            }
            $this->writeLog("UserLoginSucceeded", null, $user);
            return $this->response()->response(null);
        } else {
            $this->writeLog("UserLoginFailed", null, $user);
            return $this->response()->response(null, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/user/fastLogin", name="fastLogin")
     */
    public function fastLogin(Request $request) {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $user = $this->getUser();
        $session = $request->getSession();
        if (!$session)
            $session = new Session();
        $session->start();
        $session->set("user_token", $user->getToken());
        $response = new RedirectResponse("/");
        return $response;

    }



    /**
     * @Route("/user/reset", methods="POST")
     */
    public function reset(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator)
    {
        if (!$this->verifyCaptcha($request->request->get("captcha")))
            return $this->response()->response($translator->trans("incorrect-password"), Response::HTTP_UNAUTHORIZED);

        $target = $this->getTarget($request);
        if (is_null($target))
            return $this->response()->response($translator->trans("incorrect-phone-or-email"), Response::HTTP_UNAUTHORIZED);
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(User::class);
        if ($this->notification()->verify($target, $request->request->get("code"), NotificationService::ACTION_RESET)) {
            if ($target instanceof PhoneNumber) {
                $util = PhoneNumberUtil::getInstance();
                $phone = $util->format($target, PhoneNumberFormat::E164);
                $user = $repo->findOneBy(["phone" => $phone]);
            } else {
                $user = $repo->findOneBy(["email" => $target]);
            }
        } else {
            return $this->response()->response($translator->trans("incorrect-code"), Response::HTTP_UNAUTHORIZED);
        }
        $password = $request->request->get("password");
        if ($this->verifyPassword($user, $password)) {
            $user->setPassword($passwordEncoder->encodePassword($user, $password));
        } else {
            return $this->response()->response($translator->trans("illegal-password"), Response::HTTP_UNAUTHORIZED);
        }
        $em->persist($user);
        $em->flush();
        $this->writeLog("UserPasswordReset", null, $user);
        return $this->response()->response(null);
    }

    /**
     * @Route("/user/logout", methods="POST")
     */
    public function logout(Request $request)
    {
        if (!$this->verfityCsrfToken($request->request->get("_csrf"), AbstractController::CSRF_USER))
            return $this->response()->response("csrf.invalid", Response::HTTP_BAD_REQUEST);
        $response = $this->response()->response(null, 200);
        $time = new \DateTime();
        $time->sub(new \DateInterval("P1M"));
        $response->headers->setCookie(new Cookie("remember_token", "deleted", $time, "/", null, false, true));
        $response->headers->setCookie(new Cookie("PHPSESSID", "deleted", $time, "/", null, false, true));
        return $response;
    }

    /**
     * @Route("/user/current", name="User(Current)", methods="GET")
     */
    public function current()
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        if (null === $this->getUser())
            return $this->response()->response(null, Response::HTTP_NO_CONTENT);
        $info = $this->getUser()->getInfoArray();
        $info["unread"] = $this->getDoctrine()->getManager()->getRepository(Chat::class)->getInboxCount($this->getUser());
        return $this->response()->responseRawEntity($info);
    }

    /**
     * @Route("/user/wiki", name="User(Wiki)", methods="GET")
     */
    public function wiki()
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        if (null === $this->getUser())
            return $this->response()->response(null, Response::HTTP_NO_CONTENT);
        return new JsonResponse(array("user" => $this->getUser()->getInfoArray()));
    }

    /**
     * @Route("/user/forum", name="User(OAuth)", methods="GET")
     */
    public function forum()
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        if (null === $this->getUser())
            return $this->response()->response(null, Response::HTTP_NO_CONTENT);
        return new JsonResponse($this->getUser()->getInfoArray());
    }

    /**
     * @Route("/user/openid", name="User(OpenId)", methods="GET")
     */
    public function openid()
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        if (null === $this->getUser())
            return $this->response()->response(null, Response::HTTP_NO_CONTENT);
        return new JsonResponse(array(
            "name" => $this->getUser()->getUsername(),
            "email" => $this->getUser()->getEmail()
        ));
    }

    /**
     * @Route("user/info", name="User(Info)", methods="GET")
     */
    public function info(Request $request)
    {
        $info = $request->query->get("info") ?? "";
        $repo = $this->getDoctrine()->getManager()->getRepository(User::class);
        $user = $repo->search($info);
        if (@null === $user) {
            return $this->response()->response(null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->response()->responseEntity($user);
    }

    /**
     * @Route("user/change", methods="POST")
     */
    public function change(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        if ($passwordEncoder->isPasswordValid($this->getUser(), $request->request->get("password"))) {
            $newPassword = $request->request->get("newPassword");
            $unbindEmail = $request->request->get("unbindEmail");
            $newEmail = $request->request->get("newEmail");
            $unbindPhone = $request->request->get("unbindPhone");
            $newPhone = $request->request->get("newPhone");
            $country = $request->request->get("country");
            $phoneCode = $request->request->get("phoneCode") ?? "";
            $emailCode = $request->request->get("emailCode") ?? "";
            $user = $this->getUser();
            if ($newPassword) {
                if (!$this->verifyPassword($user, $newPassword))
                    return $this->response()->response($translator->trans("illegal-password"), Response::HTTP_UNAUTHORIZED);
                $password = $passwordEncoder->encodePassword($this->getUser(), $newPassword);
                $user->setPassword($password);
            } else if ($unbindEmail) {
                if ($user->getPhone()) {
                    $user->setEmail(null);
                } else {
                    return $this->response()->response($translator->trans("phone-not-bind"));
                }
            } else if ($newEmail) {
                if ($this->notification()->verify($newEmail, $emailCode, NotificationService::ACTION_BIND))
                    $user->setEmail($newEmail);
                else
                    return $this->response()->response($translator->trans("incorrect-code"), Response::HTTP_UNAUTHORIZED);
            } else if ($unbindPhone) {
                if ($user->getEmail()) {
                    $user->setPhone(null);
                } else {
                    return $this->response()->response($translator->trans("email-not-bind"), Response::HTTP_UNAUTHORIZED);
                }
            } else if ($newPhone) {
                $util = PhoneNumberUtil::getInstance();
                try {
                    $phone = $util->parse($newPhone, $country);
                    if ($this->notification()->verify($phone, $phoneCode, NotificationService::ACTION_BIND))
                        $user->setPhone($util->format($phone, PhoneNumberFormat::E164));
                    else
                        return $this->response()->response($translator->trans("incorrect-code"), Response::HTTP_UNAUTHORIZED);
                } catch (NumberParseException $e) {
                    return $this->response()->response($translator->trans("incorrect-phone-or-email"), Response::HTTP_UNAUTHORIZED);
                }
            }
            $this->writeLog("UserSecurityChanged");
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->response()->response(null, Response::HTTP_OK);
        } else {
            return $this->response()->response(null, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/user/avatar", methods="POST")
     */
    public function avatar(Request $request)
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $originalPhoto = $request->files->get("photo");
        $path = $originalPhoto->getRealPath();
        $avatar = new \Imagick($path);
        $height = $avatar->getImageHeight();
        $width = $avatar->getImageWidth();
        if ($height > $width) {
            $avatar->cropImage($width, $width, 0, ($height - $width) / 2);
        } else {
            $avatar->cropImage($width, $width, ($width - $height) / 2, 0);
        }
        $avatar->resizeImage(200, 200, \Imagick::FILTER_LANCZOS, 1);
        $avatar->setImageFormat("png");
        $avatar->writeImage($this->get('kernel')->getRootDir() . "/../public/avatar/" . strval($this->getUser()->getId()) . ".png");
        return $this->response()->response(null, Response::HTTP_OK);
    }

    /**
     * @Route("/user/rename", methods="POST")
     */
    public function rename(Request $request, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $username = $request->request->get("username");
        if ($this->verifyUsername($username)) {
            if ($this->getUser()->getPoint() >= 2) {
                $this->getUser()->minusPoints(2);
                $this->getUser()->setUsername($username);
                $em = $this->getDoctrine()->getManager();
                $em->persist($this->getUser());
                $em->flush();
                $this->writeLog("UserRenamed");
                return $this->response()->response(null);
            } else {
                return $this->response()->response($translator->trans("not-enough-points"), Response::HTTP_FORBIDDEN);
            }

        } else {
            return $this->response()->response($translator->trans("illegal-username"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @Route("/user/csrf")
     */
    public function getCsrfToken(Request $request)
    {
        /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrf */
        $csrf = $this->get('security.csrf.token_manager');
        return $this->response()->response($csrf->refreshToken($request->query->get("name") ?? "")->getValue());
    }

    /**
     * @Route("/user/weChat", methods="POST")
     */
    public function weChat(Request $request) {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $user = $this->getUser();
        $token = $this->verifyWeChat($request->request->get("token"));
        if(!$token)
            return $this->response()->response(null, Response::HTTP_UNAUTHORIZED);

        $openid = $token["openid"];

        $manager = $this->getDoctrine()->getManager();

        $existingUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(["weChatToken" => $openid]);
        if (!is_null($existingUser)) {
            /** @var $existingUser User */
            $existingUser->setWeChatToken(null);
            $manager->persist($existingUser);
            $this->writeLog("UserWeChatConflict", "Replaced by: " . (string)$user->getId(), $existingUser);
        }

        $user->setWeChatToken($openid);
        $manager->persist($user);
        $this->writeLog("UserWeChatBind", null, $user);
        $manager->flush();

        return $this->response()->response(null);
    }

    /**
     * @Route("/user/weChatLogin", methods="POST")
     */
    public function weChatLogin(Request $request) {
        $token = $this->verifyWeChat($request->request->get("token"));
        if(!$token)
            return $this->response()->response(null, Response::HTTP_UNAUTHORIZED);

        $openid = $token["openid"];
        $sessionKey = $token["session_key"];


        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(["weChatToken" => $openid]);

        if(is_null($user)) {
            return $this->response()->response(null, Response::HTTP_UNAUTHORIZED);
        } else {
            /** @var User $user */
            $session = $request->getSession();
            if (!$session)
                $session = new Session();
            $session->start();
            $session->set("user_token", $user->getToken());
            $session->set("wechat_session_key", $sessionKey);
            $this->writeLog("UserWeChatLogin", null, $user);
            return $this->response()->response(null);
        }
    }

    /**
     * @Route("/user/weChatLogout", methods="POST")
     */
    public function weChatLogout() {

    }

    private function verifyWeChat($code) {
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $_ENV["WECHAT_APP_ID"] . "&secret=" . $_ENV["WECHAT_APP_SECRET"] . "&js_code=$code&grant_type=authorization_code";
        $client = new Client();
        $result = $client->get($url);
        $data = json_decode($result->getBody(), true);
        if(isset($data["openid"]))
            return $data;
        else
            return false;
    }
}
