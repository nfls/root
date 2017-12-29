<?php

namespace App\Controller;

use App\Model\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

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

        //$phone = $request->request->get("phone");
        //$country = $request->request->get("country");
        //$util = \libphonenumber\PhoneNumberUtil::getInstance();

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


        /*
        if($request->request->has("code")){
            $code = $request->request->get("code");
            $phone = $request->request->get("phone");
            $codes = $em->getRepository(Code::class)->verifyDomesticCode($phone,$code,"register");
            if ($codes === false){
                return $this->response->response("短信验证码不正确",400);
            } else {
                $em->remove($codes);
                $em->flush();
                $user->setPhone($phone);
            }
        }
        */

        $em->persist($user);
        $em->flush();

        return $this->response->response(null, 200);
    }

    /**
     * @Route("/user/login", name="login", methods="POST")
     */
    public function login(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        $user = $this->getDoctrine()->getRepository(User::class)->findByUsername($request->request->get("username"));
        $respone = new JsonResponse();
        if ($passwordEncoder->isPasswordValid($user, $request->request->get("password", $user->getSalt()))) {

        }
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
