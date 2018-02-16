<?php

namespace App\Controller\School;

use App\Controller\AbstractController;
use App\Entity\School\Claz;
use App\Entity\School\Notice;
use App\Entity\User\User;
use App\Model\Permission;
use App\Service\AliyunOSS;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlackBoardController extends AbstractController
{
    /**
     * @Route("/school/blackboard/list",methods="GET")
     */
    public function list(Request $request){
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $this->denyAccessUnlessGranted(Permission::IS_STUDENT);

        return $this->response()->responseEntity($this->getUser()->getClasses()->map(function($val){
            /** @var $val Claz */
            return array(
                "id" => $val->getId(),
                "title" => $val->getTitle()
            );
        }));
    }

    /**
     * @Route("/school/blackboard/create", methods="POST")
     */
    public function create(Request $request){
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        if(!$this->getUser()->hasRole(Permission::IS_ADMIN) && !$this->getUser()->hasRole(Permission::IS_TEACHER))
            throw $this->createAccessDeniedException();
        $class = new Claz();
        $class->setTitle($request->request->get("title"));
        $class->setTeacher($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($class);
        $em->persist($this->getUser());
        $em->flush();
        return $this->response()->response(null);
    }

    /**
     * @Route("/school/blackboard/detail", methods="GET")
     */
    public function detail(Request $request) {
        $id = $request->query->get("id");
        $repo = $this->getDoctrine()->getManager()->getRepository(Claz::class);
        /** @var $class  Claz*/
        $class = $repo->findOneBy(["id"=>$id]);
        if(!is_null($class) & ($class->getStudents()->contains($this->getUser()) || $this->getUser()->hasRole(Permission::IS_ADMIN))){
            if(!$request->query->has("page")){
                return $this->response()->responseEntity($class);
            }else{
                return $this->response()->responseEntity($class->nextNotices($request->query->getInt("page",0)));
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Route("/school/blackboard/edit", methods="POST")
     */
    public function edit(Request $request){
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        if(!$this->getUser()->hasRole(Permission::IS_ADMIN) && !$this->getUser()->hasRole(Permission::IS_TEACHER))
            throw $this->createAccessDeniedException();
        $id = $request->query->get("id");
        $repo = $this->getDoctrine()->getManager()->getRepository(Claz::class);
        /** @var $class  Claz*/
        $class = $repo->findOneBy(["id"=>$id]);
        if(!is_null($class) & ($class->getTeacher() === $this->getUser() || $this->getUser()->hasRole(Permission::IS_ADMIN))){
            $uid = $request->request->get("id");
            $userRepo = $this->getDoctrine()->getManager()->getRepository(User::class);
            $user = $userRepo->find($uid);
            if($request->request->has("add"))
                $class->addStudent($user);
            else
                $class->removeStudent($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($class);
            $em->flush();
            return $this->response()->response(null);
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Route("/school/blackboard/signature", methods="GET")
     */
    public function signature(Request $request){
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        if(!$this->getUser()->hasRole(Permission::IS_ADMIN) && !$this->getUser()->hasRole(Permission::IS_TEACHER))
            throw $this->createAccessDeniedException();
        $oss = new AliyunOSS();
        return $this->response()->response($oss->privateUploadSignature($request->query->get("object"),$request->query->get("type")));
    }

    /**
     * @Route("/school/blackboard/post", methods="POST")
     */
    public function post(Request $request){
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        if(!$this->getUser()->hasRole(Permission::IS_ADMIN) && !$this->getUser()->hasRole(Permission::IS_TEACHER))
            throw $this->createAccessDeniedException();
        $id = $request->query->get("id");
        $repo = $this->getDoctrine()->getManager()->getRepository(Claz::class);
        /** @var $class  Claz*/
        $class = $repo->findOneBy(["id"=>$id]);
        if(!is_null($class) & ($class->getTeacher() === $this->getUser() || $this->getUser()->hasRole(Permission::IS_ADMIN))){
            $notice = new Notice();
            $notice->setContent($request->request->get("content"));
            $notice->setAttachment($request->request->get("files"));
            $time = \DateTime::createFromFormat("Y-m-d\TH:i:s\.???\Z",$request->request->get("time"));
            if($time === false || $time < new \DateTime())
                $time = new \DateTime();
            $notice->setTime($time);
            $ddl = \DateTime::createFromFormat("Y-m-d\TH:i:s\.???\Z",$request->request->get("deadline"));
            if($ddl === false || $ddl < new \DateTime())
                $ddl = null;
            else
                $notice->setTitle($request->request->get("title"));
            $notice->setDeadline($ddl);
            $notice->setClaz($class);
            $em = $this->getDoctrine()->getManager();
            $em->persist($notice);
            $em->flush();
            return $this->response()->response(null);
        }else{
            throw $this->createAccessDeniedException();
        }
    }
}