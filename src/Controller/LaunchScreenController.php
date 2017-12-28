<?php

namespace App\Controller;

use App\Entity\LaunchScreen;
use App\Model\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LaunchScreenController extends Controller
{

    private $response;

    public function __construct()
    {
        $this->response = new ApiResponse();
    }

    /**
     * @Route("/launchScreen/get", name="LaunchScreen(Get Image)", methods="GET")
     */
    public function getImage(){
        $em = $this->getDoctrine()->getManager()->getRepository(LaunchScreen::class);
        //TODO: Check admin
        return $this->response->response($em->getLatestImage());
    }

}
