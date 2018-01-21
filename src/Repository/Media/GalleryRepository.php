<?php

namespace App\Repository\Media;

use App\Entity\Media\Gallery;
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

    public function getAllList(){
        return $this->createQueryBuilder("u")
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getGallery($id){
        return $this->findOneBy(["id"=>$id]);
    }
}
