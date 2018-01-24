<?php

namespace App\Service;

use App\Entity\OAuth\Scope;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ScopeService extends Controller {

    function getScope($identifier){
        $repo = $this->getDoctrine()->getManager()->getRepository(Scope::class);
        $repo->findOneBy(["token"=>$identifier]);
    }

}