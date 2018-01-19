<?php

namespace App\Controller\Game;

use App\Entity\Game;
use App\Model\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class GameController extends Controller
{
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct()
    {
        $this->response = new ApiResponse();
    }

    /**
     * @Route("/game/list", methods="GET")
     */
    public function list(){
        return $this->response->responseEntity($this->getDoctrine()->getManager()->getRepository(Game::class)->findAll());
    }



}
