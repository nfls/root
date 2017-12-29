<?php

namespace App\Repository;

use App\Entity\Code;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CodeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Code::class);
    }

    public function getPhoneWithCodeAndAction($phone,$code,$action){
        return $this->createQueryBuilder('c')
            ->where('c.phone = :phone')
            ->setParameter("phone", $phone)
            ->andWhere('c.code = :code')
            ->setParameter("code",$code)
            ->andWhere('c.action = :action')
            ->setParameter("action", $action)
            ->getQuery()
            ->getResult();
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('c')
            ->where('c.something = :value')->setParameter('value', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
