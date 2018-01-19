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

    public function getRankByGame($game){
        return $this->createQueryBuilder("u")
            ->where("u.game = :game")
            ->setParameter("game",$game)
            ->orderBy("u.score", "DESC")
            ->getQuery()
            ->getResult();
    }

    public function getCurrentRankByGame($game,$user){
        return $this->createQueryBuilder("u")
            ->where("u.game = :game")
            ->setParameter("game",$game)
            ->andWhere("u.user = :user")
            ->setParameter("user",$user)
            ->andWhere("u.score > :score")
            ->setParameter("score",0)
            ->orderBy("u.score","DESC")
            ->getQuery()
            ->getResult();
    }
}
