<?php

namespace App\Controller\School;

use App\Controller\AbstractController;
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
        return $this->response->response($oss->getDownloadListToken($this->getUser()->getUsername()),200);
    }

    /**
     * @Route("school/pastpaper/header", methods="GET")
     */
    public function getHeader(Request $request,AliyunOSS $oss){
        return $this->response->response($this->setting()->get(SettingService::PAST_PAPER_HEADER));
    }
}
