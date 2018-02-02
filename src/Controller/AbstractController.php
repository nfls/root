<?php

namespace App\Controller;

use App\Model\ApiResponse;
use App\Service\SettingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AbstractController extends Controller
{
    /**
     * @var ApiResponse
     */
    protected $response;

    public function __construct()
    {
        $this->response = new ApiResponse();
    }

    public function setting(){
        return new SettingService($this->get('kernel')->getRootDir()."/Files/Settings.json");
    }

    public function verifyCaptcha($captcha){
        $verifyResponse = file_get_contents('https://www.recaptcha.net/recaptcha/api/siteverify?secret=' . $_ENV["GOOGLE_KEY"] . '&response=' . $captcha);
        if(json_decode($verifyResponse)->success){
            return true;
        }else{
            return false;
        }
    }
}
