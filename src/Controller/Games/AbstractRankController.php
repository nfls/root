<?php

namespace App\Controller\Games;

use App\Entity\Game;
use App\Entity\Rank;
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
            $em = $this->getDoctrine()->getManager();
            $gameRepo = $this->getDoctrine()->getManager()->getRepository(Game::class);
            $game = $gameRepo->findGame($request->request->get("game"));
            $score = new Rank();
            $score->setGame($game);
            $score->setScore($request->request->get("score"));
            $score->setUser($this->getUser());
        }

    }


}
