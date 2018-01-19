<?php

namespace App\Repository\Game;

use App\Entity\Game\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class GameRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Game::class);
    }


    public function listAll(){
        //return $this->findAll();
    }

    public function findGame($id){
        return $this->findOneBy(["id"=>$id]);
    }


}
