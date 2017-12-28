<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use App\Model\Message as MessageConstant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function getMessage(User $user){

    }

    public function getMessages(User $user,$section,$page){
        //TODO: Check a user's group
        return $this->createQueryBuilder("u")
            ->where("u.type",":type")
            ->setParameter("type",$section)
            ->orderBy("u.priority","DESC")
            ->addOrderBy("u.time","DESC")
            ->setFirstResult(($page - 1) * 10)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
