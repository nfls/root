<?php

namespace App\Repository\User;

use App\Entity\User\Chat;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ChatRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    public function getInbox(User $user,$page){
        return $this->createQueryBuilder("u")
            ->where("u.receiver = :receiver")
            ->setParameter("receiver", $user)
            ->orderBy("u.time","DESC")
            ->setFirstResult(($page - 1)*20)
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }

    public function getOutbox(User $user,$page){
        return $this->createQueryBuilder("u")
            ->where("u.sender = :sender")
            ->setParameter("sender", $user)
            ->orderBy("u.time","DESC")
            ->setFirstResult(($page - 1)*20)
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }
}
