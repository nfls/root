<?php

namespace App\Controller\User;

use App\Entity\User\Code;
use App\Entity\User\User;
use App\Model\ApiResponse;
use App\Service\AliyunSMS;
use App\Service\NexmoSMS;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CodeController extends Controller
{
    /**
     * @var AliyunSMS
     */
    private $aliyunService;

    /**
     * @var NexmoSMS
     */
    private $nexmoService;

    /**
     * @var ApiResponse
     */

    private $api;

    /**
     * @var User
     */
    private $user;

    /**
     * @var integer
     */
    private $code;

    /**
     * @var ApiResponse
     */
    private $response;

    /**
     * CodeController constructor.
     */
    public function __construct()
    {
        $this->aliyunService = new AliyunSMS();
        $this->nexmoService = new NexmoSMS();
        $this->api = new ApiResponse();
        $this->code = mt_rand(100000, 999999);
        $this->response = new ApiResponse();
    }

    /**
     * @Route("/code/register", methods="POST", name="sendRegisterCode")
     */
    public function sendRegisterCode(Request $request){
        $phone = intval($request->request->get("phone"));
        $country = $request->request->get("country");
        return $this->send($country,$phone,"register",AliyunSMS::REGISTER);
    }

    /**
     * @Route("/code/recover", methods="GET", name="sendResetCode")
     */
    public function sendRecoverCode(Request $request){
        $em = $this->getDoctrine()->getManager()->getRepository(User::class);
        $user = $em->search($request->query->get("info"));
        if(@is_null($user))
            return $this->response->response("用户不存在",400);
        $phone = $user->getPhone();
        if(is_null($phone)){
            return $this->api->response(null,400);
        }
        return $this->directlySend($phone,"reset",AliyunSMS::RECOVER);
    }

    /**
     * @Route("/code/change", methods="GET", name="sendChangeCode")
     */
    public function sendResetCode(Request $request){
        $phone = $this->getUser()->getPhone();
        if(is_null($phone)){
            return $this->api->response(null,400);
        }
        $phone = intval($request->request->get("phone"));
        return $this->send(null,$phone,"unbind",AliyunSMS::RECOVER);
    }

    /**
     * @Route("/code/bind", methods="GET", name="sendBindCode")
     */
    public function sendBindCode(Request $request){
        $phone = $request->query->get("phone");
        if(!is_null($phone)){
            return $this->api->response(null,400);
        }
        return $this->send(null,$phone,"bind",AliyunSMS::BIND);
    }

    /**
     * @Route("/code/unbind", methods="GET", name="sendUnBindCode")
     */
    public function sendUnBindCode(Request $request){
        $phone = $this->getUser()->getPhone();
        if(is_null($phone)){
            return $this->api->response(null,400);
        }
        return $this->directlySend($phone,"unbind",AliyunSMS::UNBIND);
    }

    /**
     * @param $phoneObject \libphonenumber\PhoneNumber
     * @param $action
     * @param $type
     * @return JsonResponse
     */
    private function directlySend($phoneObject,$action,$type){
        $util = \libphonenumber\PhoneNumberUtil::getInstance();
        if($phoneObject->getCountryCode() == 86){
            $this->sendDomestic($phoneObject->getNationalNumber(),$action,$type);
        }else{
            $this->sendInternational($util->format($phoneObject,\libphonenumber\PhoneNumberFormat::E164),$action);
        }
        return $this->response->response(null,200);
    }

    private function send($country,$phone,$action,$type){
        $util = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $phoneObject = $util->parse($phone,$country);
            $phoneE164 = $util->format($phoneObject,\libphonenumber\PhoneNumberFormat::E164);
            $em = $this->getDoctrine()->getManager()->getRepository(User::class);
            if(@!is_null($em->findByPhone($phoneE164))){
                return $this->response->response("手机号已使用",403);
            }
            if($phoneObject->getCountryCode() == 86){
                $this->sendDomestic($phone,$action,$type);
            }else{
                $this->sendInternational($phoneE164,$action);
            }
            return $this->response->response(null,200);
        }catch(\libphonenumber\NumberParseException $e){
            return $this->response->response($e->getMessage(),403);
        }
    }

    private function sendInternational($phone,$action){
        $code = new Code();
        $code->setAction($action);
        $code->setCode("000000");
        $code->setDestination($phone);
        $code->setType("phone.international");
        $em = $this->getDoctrine()->getManager();
        $em->persist($code);
        $em->flush();
        $this->nexmoService->send($phone,"NFLS.IO");
    }

    private function sendDomestic($phone,$action,$type){
        $code = new Code();
        $code->setAction($action);
        $code->setCode((string)$this->code);
        $code->setDestination($phone);
        $code->setType("phone.domestic");
        $em = $this->getDoctrine()->getManager();
        $em->persist($code);
        $em->flush();
        $this->aliyunService->sendCode($phone,$this->code,$type);
    }
}
