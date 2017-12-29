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

    public function verifyDomestic($phone,$code,$action){
        return $this->createQueryBuilder('c')
            ->where('c.destination = :destination')
            ->setParameter("destination", $phone)
            ->andWhere('c.code = :code')
            ->setParameter("code",$code)
            ->andWhere('c.action = :action')
            ->setParameter("action", $action)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
