<?php

namespace App\Repository\User;

use App\Entity\User\Code;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CodeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Code::class);
    }

    public function verifyCode($phone, $code, $action)
    {
        return $this->createQueryBuilder('c')
            ->where('c.destination = :destination')
            ->setParameter("destination", $phone)
            ->andWhere('c.code = :code')
            ->setParameter("code", $code)
            ->andWhere('c.action = :action')
            ->setParameter("action", $action)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getRequestId($phone, $action)
    {
        return $this->createQueryBuilder('c')
            ->where('c.destination = :destination')
            ->setParameter("destination", $phone)
            ->andWhere('c.action = :action')
            ->setParameter("action", $action)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
