<?php

namespace App\Controller\User;

use App\Model\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends Controller
{

    const IOS_REQUIRED_VERSION = "1.2.3";
    const IOS_ALUMNI_VERSION = "1.2.3";
    const IOS_LATEST_VERSION = "1.2.6";

    const ANDROID_REQUIRED_VERSION = "0.1.3";
    const ANDROID_LATEST_VERSION = "0.1.3";
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct()
    {
        $this->response = new ApiResponse();
    }

    /**
     * @Route("/device", name="device")
     */
    public function index()
    {
        // replace this line with your own code!
        return $this->render('@Maker/demoPage.html.twig', [ 'path' => str_replace($this->getParameter('kernel.project_dir').'/', '', __FILE__) ]);
    }

    /**
     * @Route("/device/version/iOS",name="Device(Check iOS App Version)",methods="GET")
     */
    public function checkAppleVersion(Request $request){
        $version = $request->request->get("version");
        if(version_compare($version,self::IOS_LATEST_VERSION,">=")){
            return $this->response->response(null,1000);
        }else if (version_compare($version,self::IOS_ALUMNI_VERSION,">=")){
            return $this->response->response(null,1001);
        }else if(version_compare($version,self::IOS_REQUIRED_VERSION,">=")){
            return $this->response->response(null,1002);
        }else{
            return $this->response->response(null,1003);
        }
    }

    /**
     * @Route("/device/version/android",name="Device(Check Android App Version)",methods="GET")
     */
    public function checkAndroidVersion(Request $request){
        $version = $request->request->get("version");
        if(version_compare($version,self::ANDROID_LATEST_VERSION,">=")){
            return $this->response->response(null,1000);
        }else if(version_compare($version,self::ANDROID_REQUIRED_VERSION,">=")){
            return $this->response->response(null,1002);
        }else{
            return $this->response->response(null,1003);
        }
    }
}
