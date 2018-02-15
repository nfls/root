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
    public function getClass(Request $request){
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $this->denyAccessUnlessGranted(Permission::IS_STUDENT);
        return $this->response->responseEntity($this->getUser()->getClasses());
    }
    /**
     * @Route("/school/blackboard/create", methods="POST")
     */
    public function createClass(Request $request){
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        if(!$this->getUser()->hasRole(Permission::IS_ADMIN) && !$this->getUser()->hasRole(Permission::IS_TEACHER))
            throw $this->createAccessDeniedException();
        $class = new Claz();
        $class->setTitle($request->request->get("title"));
        $class->setTeacher($this->getUser());
        //$this->getUser()-
        $em = $this->getDoctrine()->getManager();
        $em->persist($class);
        $em->persist($this->getUser());
        $em->flush();
        return $this->response->response(null);
    }
}