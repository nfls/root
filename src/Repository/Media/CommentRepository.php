<?php

namespace App\Repository\Media;

use App\Entity\Media\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function getList($page,$pagesize){
        return $this->createQueryBuilder("u")
            ->orderBy("u.time","DESC")
            ->setMaxResults($pagesize)
            ->setFirstResult(($page - 1)*$pagesize)
            ->getQuery()
            ->getResult();
    }

    public function getCount(){
        return $this->createQueryBuilder("u")
            ->select("count(u)")
            ->orderBy("u.time","DESC")
            ->getQuery()
            ->getSingleScalarResult();
    }
}
