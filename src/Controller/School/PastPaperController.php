<?php

namespace App\Controller\School;

use App\Controller\AbstractController;
use App\Entity\Preference;
use App\Model\Permission;
use App\Service\AliyunOSS;
use App\Service\SettingService;
use Nexmo\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class PastPaperController extends AbstractController
{

    /**
     * @Route("school/pastpaper/token", methods="GET")
     */
    public function getSts(Request $request, AliyunOSS $oss, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(Permission::IS_AUTHENTICATED);
        if(!$this->getUser()->hasRole(Permission::HAS_PHONE))
            return $this->response()->response($translator->trans("phone-not-bind"), 400);
        return $this->response()->response($oss->getDownloadListToken($this->getUser()->getUsername()), 200);
    }

    /**
     * @Route("school/pastpaper/header", methods="GET")
     */
    public function getHeader()
    {
        $this->denyAccessUnlessGranted(Permission::IS_AUTHENTICATED);
        return $this->response()->response($this->setting()->get(Preference::SCHOOL_PASTPAPER_HEADER));
    }
}
