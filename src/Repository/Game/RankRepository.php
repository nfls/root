<?php

namespace App\Repository\Game;

use App\Entity\Game\Rank;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RankRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Rank::class);
    }

    public function getRankByGame($game)
    {
        if ($game->isPreferBigger()) {
            return $this->createQueryBuilder("u")
                ->where("u.game = :game")
                ->setParameter("game", $game)
                ->orderBy("u.score", "DESC")
                ->getQuery()
                ->getResult();
        } else {
            return $this->createQueryBuilder("u")
                ->where("u.game = :game")
                ->setParameter("game", $game)
                ->orderBy("u.score", "ASC")
                ->getQuery()
                ->getResult();
        }

    }


    public function getCurrentRankByGame($game, $user)
    {
        if ($game->isPreferBigger()) {
            return $this->createQueryBuilder("u")
                ->where("u.game = :game")
                ->setParameter("game", $game)
                ->andWhere("u.user = :user")
                ->setParameter("user", $user)
                ->andWhere("u.score >= :score")
                ->setParameter("score", -1)
                ->orderBy("u.score", "DESC")
                ->getQuery()
                ->getResult();
        } else {
            return $this->createQueryBuilder("u")
                ->where("u.game = :game")
                ->setParameter("game", $game)
                ->andWhere("u.user = :user")
                ->setParameter("user", $user)
                ->andWhere("u.score >= :score")
                ->setParameter("score", -1)
                ->orderBy("u.score", "ASC")
                ->getQuery()
                ->getResult();
        }
    }

    public function getAllRankByUser($user)
    {
        return $this->createQueryBuilder("u")
            ->where("u.user = :user")
            ->setParameter("user", $user)
            ->getQuery()
            ->getResult();
    }

}
