<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;


class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function register($username,$password,$email)
    {


    }

    public function findByUsername($username) {
        return $this->createQueryBuilder("u")
            ->where("u.username = :username")
            ->setParameter("username", $username)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    public function fvvindByToken($token) {
        return $this->createQueryBuilder("u")
            ->where("u.token = :token")
            ->setParameter("token", $token)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
    //static function getRepos


    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('u')
            ->where('u.something = :value')->setParameter('value', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
