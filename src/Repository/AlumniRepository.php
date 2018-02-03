<?php

namespace App\Repository;

use App\Entity\Alumni;
use App\Service\AliyunOSS;
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

    public function getLastSuccessfulAuth($user) {
        $tickets =  $this->createQueryBuilder("u")
            ->where("u.user = :user")
            ->setParameter("user",$user)
            ->andWhere("u.status = :status")
            ->setParameter("status",Alumni::STATUS_PASSED)
            ->orderBy("u.submitTime","DESC")
            ->getQuery()
            ->getResult();
        $result = array_filter($tickets,function($val){
            return $val->getStatus() == Alumni::STATUS_PASSED;
        });
        if(isset($result[0]))
            return $result[0];
        else
            return null;
    }

    public function getToReview(){
        return $this->createQueryBuilder("u")
            ->where("u.status = :submitted")
            ->setParameter("submitted", Alumni::STATUS_SUBMITTED)
            ->orWhere("u.status = :reviewing")
            ->setParameter("reviewing",Alumni::STATUS_REVIEWING)
            ->orderBy("u.submitTime","ASC")
            ->getQuery()
            ->getResult();
    }
}
