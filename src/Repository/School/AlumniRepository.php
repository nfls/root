<?php

namespace App\Repository\School;

use App\Entity\School\Alumni;
use App\Service\AliyunOSS;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class AlumniRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Alumni::class);
    }

    public function getAuths($user){
        return $this->createQueryBuilder("u")
            ->where("u.user = :user")
            ->setParameter("user",$user)
            ->orderBy("u.submitTime","DESC")
            ->getQuery()
            ->getResult();
    }

    public function getLastSuccessfulAuth($user) {
        $tickets =  $this->createQueryBuilder("u")
            ->where("u.user = :user")
            ->setParameter("user",$user)
            ->andWhere("u.status = :status")
            ->setParameter("status",Alumni::STATUS_PASSED)
            ->orderBy("u.submitTime","DESC")
            ->getQuery()
            ->getResult();
        $result = array_filter($tickets,function($val){
            return $val->getStatus() == Alumni::STATUS_PASSED;
        });
        if(isset($result[0]))
            return $result[0];
        else
            return null;
    }

    public function getToReview(){
        return $this->createQueryBuilder("u")
            ->where("u.status = :submitted")
            ->setParameter("submitted", Alumni::STATUS_SUBMITTED)
            ->orWhere("u.status = :reviewing")
            ->setParameter("reviewing",Alumni::STATUS_REVIEWING)
            ->orderBy("u.submitTime","ASC")
            ->getQuery()
            ->getResult();
    }

    public function directoryQuery(Request $request){
        $query = $this->createQueryBuilder("u")
            ->where("u.status = :status")
            ->setParameter("status",5);
        $query = $this->addQuery("juniorSchool",$request,$query);
        $query = $this->addQuery("seniorSchool",$request,$query);
        $query = $this->addQuery("juniorRegistration",$request,$query);
        $query = $this->addQuery("seniorRegistration",$request,$query);
        $query = $this->addLikeQuery("university",$request,$query);
        $query = $this->addLikeQuery("major",$request,$query);
        $query = $this->addLikeQuery("workInfo",$request,$query);
        return $query->setMaxResults(30)->getQuery()->getResult();
    }

    private function addQuery($key,Request $request,QueryBuilder $query){
        if($request->request->has($key) && $request->request->get($key) != ""){
            return $query->andWhere("u.".$key." = :".$key)
                ->setParameter($key,$request->request->get($key));
        }else{
            return $query;
        }
    }

    private function addLikeQuery($key,Request $request,QueryBuilder $query){
        if($request->request->has($key) && $request->request->get($key) != ""){
            return $query->andWhere("u.".$key." like :".$key)
                ->setParameter($key,"%".$request->request->get($key)."%");
        }else{
            return $query;
        }
    }
}
