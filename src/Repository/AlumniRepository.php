<?php

namespace App\Repository;

use App\Entity\Alumni;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AlumniRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Alumni::class);
    }

    public function getAuths($user){
        return $this->createQueryBuilder("u")
            ->where("u.user = :user")
            ->setParameter("user",$user)
            ->orderBy("u.submitTime","DESC")
            ->getQuery()
            ->getResult();
    }
}
