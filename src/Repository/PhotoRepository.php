<?php

namespace App\Repository;

use App\Entity\Gallery;
use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    /**
     * @param $page int
     * @param $pagesize int
     * @param $showAll boolean
     * @param $belongTo Gallery
     * @param $startTime \DateTime
     * @param $endTime \DateTime
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getList($page,$pagesize,$showAll = true, $belongTo = null,$startTime = null,$endTime = null){

        $query = $this->createQueryBuilder("u");

        if(!$showAll){
            if(@is_null($belongTo))
                $query = $query->where("u.gallery is NULL");
            else
                $query = $query->where("u.gallery = :gallery")->setParameter("gallery",$belongTo);
        }

        return $query->setMaxResults($pagesize)->setFirstResult(($pagesize) * ($page - 1))->getQuery()->getResult();

    }
}
