<?php

namespace App\Controller\Alumni;

use App\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AlumniController extends AbstractController
{
    /**
     * @Route("alumni/submit",methods="POST")
     */
    public function submitForm(Request $request){

    }

    /**
     * @Route("alumni/form",methods="GET")
     */
    public function getForm(Request $request){
        return $this->response->response(json_decode(file_get_contents($this->get('kernel')->getRootDir()."/Controller/Alumni/Form.json")));
    }
}
