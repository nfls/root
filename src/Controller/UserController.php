<?php

namespace App\Controller;

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

    function __construct()
    {

    }

    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        // replace this line with your own code!
        return $this->render('@Maker/demoPage.html.twig', [ 'path' => str_replace($this->getParameter('kernel.project_dir').'/', '', __FILE__) ]);
    }

    /**
     * @Route("/user/register", name="register", methods="POST")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setUsername($request->request->get("username"));
        $password = $passwordEncoder->encodePassword($user,$request->request->get("password"));
        $user->setPassword($password);
        $user->setEmail($request->request->get("email"));
        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        $response = new JsonResponse();
        if(count($errors) > 0){
            $response->setData(array("error"=>(string)$errors));
        } else {
            $em->persist($user);
            $em->flush();
            $response->setData(array('data' => 123));
        }

        //$user->get


        return $response;
    }
}
