<?php

namespace App\Controller\User;

use App\Entity\User\Code;
use App\Model\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

class UserController extends Controller
{

    private $user;
    private $response;

    function __construct()
    {
        $this->response = new ApiResponse();
    }


    /**
     * @Route("/user/register", name="register", methods="POST")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();

        $phone = $request->request->get("phone");
        $country = $request->request->get("country");
        //TODO : Detect whether the user use the phone or the email.
        $util = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $phoneObject = $util->parse($phone,$country);
            $phoneE164 = $util->format($phoneObject,\libphonenumber\PhoneNumberFormat::E164);
            $em = $this->getDoctrine()->getManager();
            if($phoneObject->getCountryCode() == 86){
                $code = $em->getRepository(Code::class)->verifyDomestic($phone,$request->request->get("code"),"register");
                if(is_null($code))
                    return $this->response->response("验证码不正确",400);
                $em->remove($code);
            }else{
                //TODO
            }
            $user->setPhone($phoneE164);
        }catch(\libphonenumber\NumberParseException $e){
            return $this->response->response($e->getMessage(),400);
        }

        $username = $request->request->get("username");
        if ($this->verifyUsername($username)) {
            $user->setUsername($username);
        } else {
            return $this->response->response("用户名不正确", 400);
        }

        $email = $request->request->get("email");
        if ($this->verifyEmail($email)) {
            $user->setEmail($email);
        } else {
            return $this->response->response("邮箱不正确", 400);
        }

        $password = $request->request->get("password");
        if ($this->verifyPassword($user, $password)) {
            $user->setPassword($passwordEncoder->encodePassword($user, $password));
        } else {
            return $this->response->response("密码不合法", 400);
        }

        $em->persist($user);
        $em->flush();

        return $this->response->response(null, 200);
    }

    /**
     * @Route("/user/login", name="login", methods="POST")
     */
    public function login(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $session = new Session();
        $user = $this->getDoctrine()->getRepository(User::class)->findByUsername($request->request->get("username"));
        if (null === $user)
            return $this->response->response(null,401);
        if ($passwordEncoder->isPasswordValid($user, $request->request->get("password", $user->getSalt()))) {
            $session->set("user_token",$user->getToken());
            if($request->request->get("remember") == "true"){
                //add cookie
            }
            return $this->response->response(null);
        }else{
            return $this->response->response(null,401);
        }
    }

    /**
     * @Route("/user/current", name="User(Current)", methods="GET")
     */
    public function current(){
        return $this->response->response($this->getUser()->getInfoArray(true),200);
    }

    /**
     * @Route("user/info", name="User(Info)", methods="GET")
     */
    public function info(Request $request){
        $info = $request->query->get("info") ?? "";
        $repo = $this->getDoctrine()->getManager()->getRepository(User::class);
        $user = $repo->search($info);
        if(@null === $user){
            return $this->response->response(null,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->response->response($user->getInfoArray($user->getUsername() == $this->getUser()->getUsername()),200);
    }


    private function verifyEmail($email)
    {
        if (preg_match("/^[a-zA-Z0-9_+.-]+\@([a-zA-Z0-9-]+\.)+[a-zA-Z0-9]{2,4}$/", $email)) {
            return true;
        } else {
            return false;
        }
    }

    private function verifyUsername($username)
    {
        $re = '/[A-Za-z0-9_\-\x{0800}-\x{9fa5}]{3,16}/u';
        if (preg_match($re, $username)) {
            return true;
        } else {
            return false;
        }

    }

    private function verifyPassword(User $user, $password)
    {
        //Strength
        $re = '/^((?=\S*?[a-zA-Z])(?=\S*?[0-9]).{6,})\S$/';
        if (!preg_match($re, $password))
            return false;
        //Email
        if (preg_match('/' . $user->getEmail() . '/', $password))
            return false;
        //Username
        if (preg_match('/' . $user->getUsername() . '/', $password))
            return false;

        return true;
    }
}
