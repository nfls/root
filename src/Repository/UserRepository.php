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
            ->getOneOrNullResult();
    }

    public function findByToken($token) {
        return $this->createQueryBuilder("u")
            ->where("u.token = :token")
            ->setParameter("token", $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByPhone($phone) {
        return $this->createQueryBuilder("u")
            ->where("u.phone = :phone")
            ->setParameter("phone", $phone)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByEmail($email){
        return $this->createQueryBuilder("u")
            ->where("u.email = :email")
            ->setParameter("email",$email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function search($info){
        return $this->createQueryBuilder("u")
            ->where("u.phone = :phone")
            ->setParameter("phone",$info)
            ->orWhere("u.email = :email")
            ->setParameter("email",$info)
            ->orWhere("u.username = :username")
            ->setParameter("username",$info)
            ->getQuery()
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
