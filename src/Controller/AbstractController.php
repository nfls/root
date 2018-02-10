<?php

namespace App\Controller;

use App\Entity\Preference;
use App\Model\ApiResponse;
use App\Service\SettingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;

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
        return $this->getDoctrine()->getManager()->getRepository(Preference::class);
    }

    public function verifyCaptcha($captcha){
        $verifyResponse = file_get_contents('https://www.recaptcha.net/recaptcha/api/siteverify?secret=' . $_ENV["GOOGLE_KEY"] . '&response=' . $captcha);
        if(json_decode($verifyResponse)->success){
            return true;
        }else{
            return false;
        }
    }

    const CSRF_USER = "user";
    const CSRF_ALUMNI_FORM = "alumni.form";
    const CSRF_MEDIA_GALLERY = "media.gallery";

    public function verfityCsrfToken($token,$id) {
        if(is_null($token))
            return false;
        else
            if($this->getUser()->isOAuth) {
                return true;
            } else {
                /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrf */
                $csrf = $this->get('security.csrf.token_manager');
                if($csrf->isTokenValid(new CsrfToken($id,$token)))
                    return true;
                else
                    return false;
            }
    }
}
