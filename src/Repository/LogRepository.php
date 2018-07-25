<?php

namespace App\Repository;

use App\Entity\Log;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function findByUser(User $user) {
        return $this->createQueryBuilder("u")
            ->where("u.user = :user")
            ->setParameter("user", $user)
            ->orderBy("u.id", "desc")
            ->setMaxResults(30)
            ->getQuery()
            ->getResult();
    }

}
