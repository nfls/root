<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class BasicController extends Controller
{

    private $user;

    private $client;

    function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {

    }

    /**
     * @Route("/basic", name="basic")
     */
    public function index()
    {
        // replace this line with your own code!
        return $this->render('@Maker/demoPage.html.twig', [ 'path' => str_replace($this->getParameter('kernel.project_dir').'/', '', __FILE__) ]);
    }

    /**
     * @Route("/basic/user",name="user8xwxsxw")
     */
    public function user(){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        //dump($user);
        return new JsonResponse($user->getUsername());
    }

}
