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
     * @Route("alumni/countries", methods="GET")
     */
    public function getCountries(){
        return $this->response->response(json_decode(file_get_contents($this->get('kernel')->getRootDir()."/Model/Countries.json")));
    }

    /**
     * @Route("alumni/info", methods="GET")
     */
    public function getInfo(){
        $repo =  $this->getDoctrine()->getManager()->getRepository(Alumni::class);
        return $this->response->responseEntity($repo->getAuths($this->getUser()));
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
     * @Route("alumni/detail", methods="GET")
     */
    public function getFormDetail(Request $request){
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Alumni::class);
        $id = $request->query->get("id");
        $form = $repo->findOneBy(["id"=>$id,"user"=>$this->getUser()]);
        return $this->response->responseEntity($form);
    }

    /**
     * @Route("alumni/save",methods="POST")
     */
    public function saveForm(Request $request){
        $id = $request->query->get("id");
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Alumni::class);
        /**
         * @var Alumni $form
         */
        $form = $repo->findOneBy(["id"=>$id,"status"=>Alumni::STATUS_NOT_SUBMITTED,"user"=>$this->getUser()]);

        $form->setUserStatus($request->request->get("userStatus"));
        $form->setChineseName($request->request->get("chineseName"));
        $form->setEnglishName($request->request->get("englishName"));
        $birthday = \DateTime::createFromFormat("Y-m-d\TH:i:s\.000\Z",$request->request->get("birthday"));
        $birthday->add(new \DateInterval("PT11H"));
        $form->setBirthday($birthday);
        $form->setGender($request->request->get("gender"));
        $form->setJuniorSchool($request->request->get("juniorSchool"));
        $form->setJuniorRegistration($request->request->get("juniorRegistration"));
        $form->setJuniorClass($request->request->get("juniorClass"));
        $form->setSeniorSchool($request->request->get("seniorSchool"));
        $form->setSeniorRegistration($request->request->get("seniorRegistration"));
        $form->setSeniorClass($request->request->get("seniorClass"));
        $form->setUniversity($request->request->get("university"));
        $form->setMajor($request->request->get("major"));
        $form->setWorkInfo($request->request->get("workInfo"));
        $form->setPersonalInfo($request->request->get("personalInfo"));
        $form->setCountry($request->request->get("country"));
        $form->setLocation($request->request->get("location"));
        $form->setOnlineContact($request->request->get("onlineContact"));
        $form->setRemark($request->request->get("remark"));
        $em->persist($form);
        $em->flush();

        return $this->response->response(null);
    }


}
