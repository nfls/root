<?php

namespace App\Repository\Media;

use App\Entity\Media\Gallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\RegistryInterface;

class GalleryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Gallery::class);
    }

    public function getList($page,$canViewPrivate = false,$canViewAll = false)
    {
        $query = $this->createQueryBuilder("u");
        if(!$canViewPrivate){
            $query = $query->where("u.isPublic = true");
        }
        if(!$canViewAll){
            $query = $query->andWhere("u.isVisible = true");
        }

        return $query->setMaxResults(10)
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

    public function getGallery($id,$canViewPrivate = false,$canViewAll = false){
        $gallery = $this->findOneBy(["id"=>$id]);
        if(null === $gallery)
            return null;
        $photos = new ArrayCollection(array_filter($gallery->getPhotos()->toArray(),function($val)use($canViewAll,$canViewPrivate){
            if(!$canViewAll)
                return $val->isVisible();
            if(!$canViewPrivate)
                return $val->isPublic();
            return true;
        }));
        $gallery->setPhotos($photos);
        return $gallery;
    }
}
