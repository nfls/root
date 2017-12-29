<?php

namespace App\Repository;

use App\Entity\LaunchScreen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LaunchScreenRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LaunchScreen::class);
    }

    public function getLatestImage(){
        return $this->createQueryBuilder("l")
            ->where("l.time <= :time")
            ->setParameter("time", new \DateTime())
            ->orderBy("l.id","DESC")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function getLatestImageForAdmin(){
        return $this->createQueryBuilder("l")
            ->orderBy("l.id","DESC")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getImageList($page){
        return $this->createQueryBuilder("l")
            ->setMaxResults(10)
            ->setFirstResult(($page - 1)*10)
            ->orderBy("l.time","DESC")
            ->getQuery()
            ->getResult();
    }
    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('l')
            ->where('l.something = :value')->setParameter('value', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
