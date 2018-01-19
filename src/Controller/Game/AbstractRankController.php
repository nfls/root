<?php

namespace App\Controller\Game;

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
        $em = $this->getDoctrine()->getManager();
        $gameRepo = $this->getDoctrine()->getManager()->getRepository(Game::class);
        $rankRepo = $this->getDoctrine()->getManager()->getRepository(Rank::class);
        $game = $gameRepo->findGame($request->query->get("game"));
        if($request->request->has("score")){
            $current = $rankRepo->getCurrentRankByGame($game,$this->getUser());
            $score = $request->request->get("score");
            if(count($current) == 0){
                $this->updateScore($game,$this->getUser(),$score);
            }else{
                if($current[0]->getScore() <= $score){
                    foreach($current as $rank){
                        $em->remove($rank);
                    }
                    $em->flush();
                    $this->updateScore($game,$this->getUser(),$score);
                }
            }
        }

        $result = $rankRepo->getRankByGame($game);
        return $this->reponse->responseEntity($result);
    }

    private function updateScore($game,$user,$score){
        $em = $this->getDoctrine()->getManager();
        if(@!is_null($score)){
            $rank = new Rank();
            $rank->setGame($game);
            $rank->setScore($score);
            $rank->setUser($user);
            $em->persist($rank);
            $em->flush();
        }

    }


}
