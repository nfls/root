<?php

namespace App\Controller\Alumni;

use App\Controller\AbstractController;
use App\Entity\Alumni;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AlumniController extends AbstractController
{

    /**
     * @Route("alumni/form",methods="GET")
     */
    public function getForm(Request $request){
        return $this->response->response(json_decode(file_get_contents($this->get('kernel')->getRootDir()."/Controller/Alumni/Form.json")));
    }

    /**
     * @Route("alumni/new", methods="POST")
     */
    public function newForm(Request $request){
        $repo =  $this->getDoctrine()->getManager()->getRepository(Alumni::class);
        if(count($repo->findBy(["user"=>$this->getUser(),"status"=>Alumni::STATUS_NOT_SUBMITTED]))>0){
            return $this->response->response("alumni.already.new",403);
        }
        $alumni = new Alumni();
        $alumni->setUser($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($alumni);
        $em->flush();
        return $this->response->response(null);
    }

    /**
     * @Route("alumni/submit",methods="POST")
     */
    public function submitForm(Request $request){

    }
}
