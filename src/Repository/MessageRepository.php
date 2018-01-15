<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use App\Model\Message as MessageConstant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function getMessage(User $user){

    }

    public function getMessages(User $user,$section,$page){
        //TODO: Check a user's group
        return $this->createQueryBuilder("u")
            ->where("u.type = :type")
            ->setParameter("type",$section)
            ->orderBy("u.priority","DESC")
            ->addOrderBy("u.time","DESC")
            ->setFirstResult(($page - 1) * 10)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function getUnreadCount(\DateTime $time){
        //TODO: Check a user's group
        return count(
            $this->createQueryBuilder("u")
            ->where("u.type = :type")
            ->setParameter("type",MessageConstant::SYSTEM_MESSAGE)
            ->andWhere("u.time >= :time")
            ->setParameter("time",$time)
            ->orderBy("u.priority","DESC")
            ->addOrderBy("u.time","DESC")
            ->getQuery()
            ->getResult()
        );
    }

    public function getAllMessages($pages,$pagesize,$type,$group){
        $query = $this->createQueryBuilder("u");
        if(@!is_null($type)){
            $query = $query->where("u.type = :type")
                ->setParameter("type",$type);
        }
        if(@!is_null($group)){
            $query = $query->andWhere("u.group = :group")
                ->setParameter("group",$group);
        }
        return $query->setMaxResults($pagesize)
            ->setFirstResult(($pages - 1)*$pagesize)
            ->getQuery()
            ->getResult();
    }

    public function getAllMessagesCount($pages,$pagesize,$type,$group){
        $query = $this->createQueryBuilder("u");
        if(@!is_null($type)){
            $query = $query->where("u.type = :type")
                ->setParameter("type",$type);
        }
        if(@!is_null($group)){
            $query = $query->andWhere("u.group = :group")
                ->setParameter("group",$group);
        }
        return intval($query->select("count(u)")->getQuery()->getSingleScalarResult());
    }
}
