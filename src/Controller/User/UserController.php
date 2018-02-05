<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\Alumni;
use App\Entity\User\Code;
use App\Model\ApiResponse;
use App\Model\Permission;
use App\Service\NexmoSMS;
use App\Service\CodeVerificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Zend\Diactoros\Request\Serializer;

class UserController extends AbstractController
{
    /**
     * @Route("/",name="index",methods="GET")
     */
    public function index()
    {
        if ($_ENV["APP_ENV"] == "dev")
            return $this->render("index.debug.html.twig");
        return $this->render("index.html.twig");
    }

    /*
    public function migrate(\Symfony\Component\HttpFoundation\Request $request)
    {
        $info = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        foreach ($info as $userInfo) {
            $user = new User();
            if (!$this->verifyUsername($userInfo["username"]))
                $user->setUsername("user" . substr(md5(microtime()), rand(0, 26), 6));
            else
                $user->setUsername($userInfo["username"]);
            $user->setPassword($userInfo["password"]);
            $user->setEmail($userInfo["email"]);

            $user->setJoinTime(\DateTime::createFromFormat("Y-m-d H:i:s", $userInfo["join_time"]));
            if (isset($userInfo["phone"]))
                $user->setPhone(intval("86" . (string)$userInfo["phone"]));
            $em->persist($user);
            if (isset($userInfo["realname"]["englishName"])) {
                $alumniInfo = $userInfo["realname"];
                $alumni = new Alumni();
                $alumni->setUserStatus(1);
                $alumni->setChineseName($alumniInfo["chineseName"]);
                $alumni->setEnglishName($alumniInfo["englishName"]);
                $alumni->setSeniorRegistration($alumniInfo["seniorRegistration"]);
                $alumni->setSeniorClass($alumniInfo["seniorClass"]);
                $alumni->setSeniorSchool($alumniInfo["seniorSchool"]);
                $alumni->setRemark("由旧版IC实名认证系统自动生成。");
                $alumni->setUser($user);
                $em->persist($alumni);
            }
        }
        $em->flush();
        return $this->response->response(null);
    }
    */

    /**
     * @Route("/user/register", name="register", methods="POST")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if (!$this->verifyCaptcha($request->request->get("captcha")))
            return $this->response->response("验证码不正确", Response::HTTP_UNAUTHORIZED);
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $sms = new CodeVerificationService($this->getDoctrine()->getManager());
        $phone = $request->request->get("phone");
        $country = $request->request->get("country");
        $code = $request->request->get("code");
        $username = $request->request->get("username");
        if ($this->verifyUsername($username)) {
            $user->setUsername($username);
        } else {
            return $this->response->response("用户名不正确", Response::HTTP_UNAUTHORIZED);
        }
        $password = $request->request->get("password");
        if ($this->verifyPassword($user, $password)) {
            $user->setPassword($passwordEncoder->encodePassword($user, $password));
        } else {
            return $this->response->response("密码不合法", Response::HTTP_UNAUTHORIZED);
        }
        if (intval($phone) > 0) {
            $phoneE164 = $sms->validate($country, $phone, $code, "register");
            if ($phoneE164 === false)
                return $this->response->response("手机验证码不正确", Response::HTTP_UNAUTHORIZED);
            $user->setPhone($phoneE164);
        } else {
            $email = $request->request->get("email");
            if ($sms->verify($email, $code, "register")) {
                $user->setEmail($email);
            } else {
                return $this->response->response("邮箱验证码不正确", Response::HTTP_UNAUTHORIZED);
            }
        }
        $em->persist($user);
        $em->flush();

        return $this->response->response(null);
    }

    /**
     * @Route("/user/login", name="login")
     */
    public function login(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if (!$this->verifyCaptcha($request->request->get("captcha")))
            return $this->response->response("验证码不正确", Response::HTTP_UNAUTHORIZED);
        $session = $request->getSession();
        if (!$session)
            $session = new Session();
        $session->start();
        $user = $this->getDoctrine()->getRepository(User::class)->search($request->request->get("username"));
        if (null === $user)
            return $this->response->response(null, 401);
        if ($passwordEncoder->isPasswordValid($user, $request->request->get("password", $user->getSalt()))) {
            $session->set("user_token", $user->getToken());

            if ($request->request->get("remember") == "true") {
                $response = $this->response->response(null);
                $time = new \DateTime();
                $time->add(new \DateInterval("P1M"));
                $response->headers->setCookie(new Cookie("remember_token", $user->getToken(), $time, "/", null, false, true));
                return $response;
            }
            return $this->response->response(null);
        } else {
            return $this->response->response(null, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/user/reset", methods="POST")
     */
    public function reset(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if (!$this->verifyCaptcha($request->request->get("captcha")))
            return $this->response->response("验证码不正确", Response::HTTP_UNAUTHORIZED);
        $sms = new CodeVerificationService($this->getDoctrine()->getManager());
        $phone = $request->request->get("phone");
        $country = $request->request->get("country");
        $code = $request->request->get("code");
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(User::class);
        if (intval($phone) > 0) {
            $phoneE164 = $sms->validate($country, $phone, $code, "reset");
            if ($phoneE164 === false)
                return $this->response->response("手机验证码不正确", Response::HTTP_UNAUTHORIZED);
            $user = $repo->findByPhone($phoneE164);
        } else {
            $email = $request->request->get("email");
            if ($sms->verify($email, $code, "reset")) {
                $user = $repo->findByEmail($email);
            } else {
                return $this->response->response("邮箱验证码不正确", Response::HTTP_UNAUTHORIZED);
            }
        }
        $password = $request->request->get("password");
        if ($this->verifyPassword($user, $password)) {
            $user->setPassword($passwordEncoder->encodePassword($user, $password));
        } else {
            return $this->response->response("密码不合法", Response::HTTP_UNAUTHORIZED);
        }
        $em->persist($user);
        $em->flush();
        return $this->response->response(null);
    }

    /**
     * @Route("/user/logout", methods="GET")
     */
    public function logout(Request $request)
    {
        $response = $this->response->response(null, 200);
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
            return $this->response->response(null, Response::HTTP_NO_CONTENT);
        return $this->response->responseRawEntity($this->getUser()->getInfoArray());
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
            return $this->response->response(null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->response->responseEntity($user);
    }

    /**
     * @Route("user/change", methods="POST")
     */
    public function change(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if($passwordEncoder->isPasswordValid($this->getUser(),$request->request->get("password"))) {
            $newPassword = $request->request->get("newPassword");
            $unbindEmail = $request->request->get("unbindEmail");
            $newEmail = $request->request->get("newEmail");
            $unbindPhone = $request->request->get("unbindPhone");
            $newPhone = $request->request->get("newPhone");
            $country = $request->request->get("country");
            $code = $request->request->get("code");
            $user = $this->getUser();
            $sms = new CodeVerificationService($this->getDoctrine()->getManager());
            if($newPassword) {
                if(!$this->verifyPassword($user,$newPassword))
                    return $this->response->response("密码太弱！",Response::HTTP_UNAUTHORIZED);
                $password = $passwordEncoder->encodePassword($this->getUser(),$newPassword);
                $user->setPassword($password);
            }else if($unbindEmail){
                if($user->getPhone()){
                    $user->setEmail(null);
                }else{
                    return $this->response->response("您没有绑定手机！");
                }
            }else if($newEmail){
                if ($sms->verify($newEmail, $code, "bind")) {
                    $user->setEmail($newEmail);
                } else {
                    return $this->response->response("邮箱验证码不正确", Response::HTTP_UNAUTHORIZED);
                }
            }else if($unbindPhone){
                if($user->getEmail()){
                    $user->setPhone(null);
                }else{
                    return $this->response->response("您没有绑定邮箱！", Response::HTTP_UNAUTHORIZED);
                }
            }else if($newPhone){
                $phoneE164 = $sms->validate($country, $newPhone, $code, "bind");
                if ($phoneE164 === false)
                    return $this->response->response("邮箱验证码不正确", Response::HTTP_UNAUTHORIZED);
                $user->setPhone($phoneE164);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->response->response(null, Response::HTTP_OK);
        }else{
            return $this->response->response(null,Response::HTTP_UNAUTHORIZED);
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
}
