<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\User\Code;
use App\Model\ApiResponse;
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
    public function index(){
        return $this->render("index.html.twig");
    }
    /**
     * @Route("/user/register", name="register", methods="POST")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
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
        if(intval($phone) > 0){
            $phoneE164 = $sms->validate($country,$phone,$code,"register");
            if($phoneE164 === false)
                return $this->response->response("手机验证码不正确",Response::HTTP_UNAUTHORIZED);
            $user->setPhone($phoneE164);
        }else{
            $email = $request->request->get("email");
            if ($sms->verify($email,$code,"register")) {
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
        $session = $request->getSession();
        if(!$session)
            $session = new Session();
        $session->start();
        $user = $this->getDoctrine()->getRepository(User::class)->findByUsername($request->request->get("username"));
        if (null === $user)
            return $this->response->response(null,401);
        if ($passwordEncoder->isPasswordValid($user, $request->request->get("password", $user->getSalt()))) {
            $session->set("user_token",$user->getToken());

            if($request->request->get("remember") == "true"){
                $response = $this->response->response(null);
                $time = new \DateTime();
                $time->add(new \DateInterval("P1M"));
                $response->headers->setCookie(new Cookie("remember_token",$user->getToken(),$time,"/",null,false,true));
                return $response;
            }
            return $this->response->response(null);
        }else{
            return $this->response->response(null,Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/user/current", name="User(Current)", methods="GET")
     */
    public function current(){
        if(null === $this->getUser())
            return $this->response->response(null,Response::HTTP_NO_CONTENT);
        return $this->response->responseUser($this->getUser()->getInfoArray());
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
        return $this->response->responseEntity($user);
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
