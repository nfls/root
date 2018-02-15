<?php

namespace App\Controller\School;

use App\Controller\AbstractController;
use App\Entity\School\Claz;
use App\Model\Permission;
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
}