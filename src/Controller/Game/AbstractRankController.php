<?php

namespace App\Controller\Game;

use App\Controller\AbstractController;
use App\Entity\Game\Game;
use App\Entity\Game\Rank;
use App\Model\ApiResponse;
use App\Model\Normalizer\GameNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AbstractRankController extends AbstractController
{
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
                if($game->isPreferBigger()){
                    if($current[0]->getScore() <= $score){
                        foreach($current as $rank){
                            $em->remove($rank);
                        }
                        $em->flush();
                        $this->updateScore($game,$this->getUser(),$score);
                    }
                }else{
                    if($current[0]->getScore() >= $score){
                        foreach($current as $rank) {
                            $em->remove($rank);
                        }
                        $em->flush();
                        $this->updateScore($game,$this->getUser(),$score);
                    }
                }
            }
        }
        $result = $rankRepo->getRankByGame($game);
        return $this->response->responseEntity($result);
    }

    private function updateScore($game,$user,$score)
    {
        $em = $this->getDoctrine()->getManager();
        if (@!is_null($score)) {
            $rank = new Rank();
            $rank->setGame($game);
            $rank->setScore($score);
            $rank->setUser($user);
            $em->persist($rank);
            $em->flush();
        }
    }

    private function phaseRank($ranks){
        $previous = -1;
        $pos = 1;
        $final = array();
        foreach ($ranks as $key => $rank){
            if($previous != $rank->getScore()){
                $previous = $rank->getScore();
                $pos = $key + 1;
            }
            $rank->setRank($pos);
            $rank->setGame((new GameNormalizer())->normalize($rank->getGame()));
            array_push($final,$rank);
        }
        return $final;
    }

}
