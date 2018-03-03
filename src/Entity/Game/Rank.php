<?php

namespace App\Entity\Game;

use App\Entity\User\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Game\RankRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Rank
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Game
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Game\Game")
     */
    private $game;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(type="bigint")
     */
    private $score;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $time;

    private $rank = -1;

    public function __construct()
    {
        $this->time = new \DateTime();
    }

    /**
     * @ORM\PostLoad
     * @ORM\PostPersist
     */
    public function calcRank(LifecycleEventArgs $args)
    {
        if ($this->game->isPreferBigger()) {
            $count = $args->getEntityManager()->getRepository(Rank::class)->createQueryBuilder("u")
                ->select("count(u.id)")
                ->where("u.score > :score")
                ->setParameter("score", $this->score)
                ->andWhere("u.game = :game")
                ->setParameter("game", $this->game)
                ->getQuery()
                ->getSingleScalarResult();
        } else {
            $count = $args->getEntityManager()->getRepository(Rank::class)->createQueryBuilder("u")
                ->select("count(u.id)")
                ->where("u.score < :score")
                ->setParameter("score", $this->score)
                ->andWhere("u.game = :game")
                ->setParameter("game", $this->game)
                ->getQuery()
                ->getSingleScalarResult();
        }

        $this->rank = $count + 1;
    }

    /**
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param Game $game
     */
    public function setGame($game)
    {
        $this->game = $game;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @param int $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return int
     */
    public function getRank(): int
    {
        return $this->rank;
    }

    /**
     * @param int $rank
     */
    public function setRank(int $rank): void
    {
        $this->rank = $rank;
    }


}
