<?php

namespace App\Repository;

use App\Entity\Preference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PreferenceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Preference::class);
    }

    public function get($identifier){
        return $this->findOneBy(["identifier"=>$identifier])->getContent();
    }

    public function set($identifier,$content){
         $preference = $this->findOneBy(["identifier"=>$identifier]);
         $preference->setContent($content);
         $this->getEntityManager()->persist($preference);
         $this->getEntityManager()->flush();
    }
}
