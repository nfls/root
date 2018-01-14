<?php

namespace App\Controller\Games;

use App\Model\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AbstractRankController extends Controller
{
    /**
     * @var ApiResponse
     */
    private $reponse;

    public function __construct()
    {
        $this->reponse = new ApiResponse();
    }

    /**
     * @Route("/game/rank")
     */
    public function rank(Request $request){
        //TODO Score Encryption
        $user = $this->getUser();
        $identifier = $request->query->get("identifier");
        if($request->request->has("score")){
            
        }

    }


}
