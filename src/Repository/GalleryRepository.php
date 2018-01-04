<?php

namespace App\Repository;

use App\Entity\Gallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class GalleryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Gallery::class);
    }

    public function getList($page)
    {
        return $this->createQueryBuilder("u")
            ->setMaxResults(10)
            ->setFirstResult(($page - 1) * 10)
            ->getQuery()
            ->getResult();
    }
}
