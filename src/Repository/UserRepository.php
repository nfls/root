<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;


/**
 * Class UserRepository
 * @package App\Repository
 */
class UserRepository extends ServiceEntityRepository implements  UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByUsername($username) {
        return $this->findOneBy(["username"=>$username]);
    }

    public function findByToken($token) {
        return $this->findOneBy(["token"=>$token]);
    }

    public function findByPhone($phone) {
        return $this->findOneBy(["phone"=>$phone]);
    }

    public function findByEmail($email){
        return $this->findOneBy(["email"=>$email]);
    }

    /**
     * @param $info
     * @return User|null
     */

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

    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $user = $this->search($username);
        if(@null === $user){
            return null;
        }else{
            if(password_verify($password,$user->getPassword())){
                return $user;
            }else{
                return null;
            }
        }
    }

}
