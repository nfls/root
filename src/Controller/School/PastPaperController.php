<?php

namespace App\Controller\School;

use App\Controller\AbstractController;
use App\Entity\Preference;
use App\Model\Permission;
use App\Service\AliyunOSS;
use App\Service\SettingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PastPaperController extends AbstractController
{

    /**
     * @Route("school/pastpaper/token", methods="GET")
     */
    public function getSts(Request $request,AliyunOSS $oss){
        $this->denyAccessUnlessGranted(Permission::IS_AUTHENTICATED);
        return $this->response->response($oss->getDownloadListToken($this->getUser()->getUsername()),200);
    }

    /**
     * @Route("school/pastpaper/header", methods="GET")
     */
    public function getHeader(){
        $this->denyAccessUnlessGranted(Permission::IS_AUTHENTICATED);
        return $this->response->response($this->setting()->get(Preference::SCHOOL_PASTPAPER_HEADER));
    }
}
