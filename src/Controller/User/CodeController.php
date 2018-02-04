<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\User\Code;
use App\Entity\User\User;
use App\Model\ApiResponse;
use App\Service\AliyunSMS;
use App\Service\MailService;
use App\Service\NexmoSMS;
use PHPMailer\PHPMailer\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CodeController extends AbstractController
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
     * @var MailService
     */
    private $mailService;

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
     * @var string
     */
    private $token;


    /**
     * CodeController constructor.
     */
    public function __construct()
    {
        $this->aliyunService = new AliyunSMS();
        $this->nexmoService = new NexmoSMS();
        $this->mailService = new MailService();
        $this->api = new ApiResponse();
        $this->code = mt_rand(100000, 999999);
        $this->token = base64_encode(random_bytes(16));
        parent::__construct();
    }

    /**
     * @Route("/code/available", methods="GET")
     */
    public function getAvailableRegion(Request $request){
        return $this->response->response(json_decode(file_get_contents($this->get('kernel')->getRootDir()."/Files/Phone.json")));
    }

    /**
     * @Route("/code/register", methods="POST", name="sendRegisterCode")
     */
    public function sendRegisterCode(Request $request){
        if(!$this->verifyCaptcha($request->request->get("captcha")))
            return $this->response->response("验证码不正确",Response::HTTP_UNAUTHORIZED);
        $phone = intval($request->request->get("phone"));
        if($phone > 0){
            $country = $request->request->get("country");
            return $this->send($country,$phone,"register",AliyunSMS::REGISTER);
        }else{
            $email = $request->request->get("email");
            return $this->sendMail($email,"register","registering a new account");
        }

    }

    /**
     * @Route("/code/reset", methods="POST", name="sendResetCode")
     */
    public function sendResetCode(Request $request){
        if(!$this->verifyCaptcha($request->request->get("captcha")))
            return $this->response->response("验证码不正确",Response::HTTP_UNAUTHORIZED);
        $em = $this->getDoctrine()->getManager()->getRepository(User::class);
        $phone = intval($request->request->get("phone"));
        $email = $request->request->get("email");
        $country = $request->request->get("country");
        if($phone > 0){
            $util = \libphonenumber\PhoneNumberUtil::getInstance();
            try {
                $phoneObject = $util->parse($phone,$country);
                $phoneE164 = $util->format($phoneObject,\libphonenumber\PhoneNumberFormat::E164);
                $user = $em->findByPhone($phoneE164);
                if(@is_null($user))
                    return $this->response->response("用户不存在",400);
                else
                    return $this->directlySend($user->getPhone(),"reset",AliyunSMS::RECOVER);
            }catch(\libphonenumber\NumberParseException $e){
                return $this->response->response("手机号格式错误",400);
            }
        }else{
            $user = $em->findByEmail($email);
            if(@is_null($user))
                return $this->response->response("用户不存在",400);
            else
                return $this->sendMail($user->getEmail(),"reset","resetting your password",false);
        }
    }

    /**
     * @Route("/code/change", methods="GET", name="sendChangeCode")
     */
    public function sendChangeCode(Request $request){
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

    private function checkUsedEmail($email){
        $em = $this->getDoctrine()->getManager()->getRepository(User::class);
        if(@!is_null($em->findByEmail($email))){
            return true;
        }else{
            return false;
        }
    }

    private function checkUsedPhone($phone){
        $em = $this->getDoctrine()->getManager()->getRepository(User::class);
        if(@!is_null($em->findByPhone($phone))){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $phoneObject \libphonenumber\PhoneNumber
     * @param $action
     * @param $type
     * @return JsonResponse
     */
    private function directlySend($phone,$action,$type){
        $util = \libphonenumber\PhoneNumberUtil::getInstance();
        if($phone->getCountryCode() == 86){
            $this->sendDomestic($phone->getNationalNumber(),$action,$type);
        }else{
            $this->sendInternational($util->format($phone,\libphonenumber\PhoneNumberFormat::E164),$action);
        }
        return $this->response->response(null,200);

    }

    private function send($country,$phone,$action,$type,$checkUsed = true){
        $util = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $phoneObject = $util->parse($phone,$country);
            $phoneE164 = $util->format($phoneObject,\libphonenumber\PhoneNumberFormat::E164);
            if($checkUsed && $this->checkUsedPhone($phoneE164))
                return $this->response->response("phone.repeated",Response::HTTP_BAD_REQUEST);
            $em = $this->getDoctrine()->getManager()->getRepository(User::class);
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
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Code::class);
        $id = $repo->getRequestId($phone,$action);
        if(null !== $id){
            $this->nexmoService->cancel($id->getCode());
            $em->remove($id);
        }
        $code = new Code();
        $code->setAction($action);
        $code->setCode($this->nexmoService->send($phone,"NFLS.IO"));
        $code->setDestination($phone);
        $code->setType("phone.international");
        $em->persist($code);
        $em->flush();
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

    private function sendMail($target,$action,$readableAction,$checkUsed = true){
        if($checkUsed && $this->checkUsedEmail($target))
            return $this->response->response("email.repeated",Response::HTTP_BAD_REQUEST);
        $banDomain = ['chacuo','027168','bccto','a7996','zv68','sohus','piaa',
            'deiie','zhewei88','11163','svip520','ado0','haida-edu',
            'sian','jy5201','chaichuang','xtianx','zymuying','dayone',
            'tianfamh','zhaoyuanedu','cuirushi','6gmu','yopmail',
            'mailinator','www.', '.cm', 'pp.com', 'loaoa', 'oiqas', 'dawin', 'instalapple', '+'];
        foreach($banDomain as $od){
            if(stripos($target, $od)!==false)
                return $this->response->response("email.notAllowed",Response::HTTP_FORBIDDEN);
        }
        $re = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
        if(!preg_match($re,$target)){
            return $this->response->response("email.notAllowed",Response::HTTP_FORBIDDEN);
        }
        $code = new Code();
        $code->setAction($action);
        $code->setDestination($target);
        $code->setCode($this->token);
        $code->setType("email");
        $em = $this->getDoctrine()->getManager();
        $em->persist($code);
        $em->flush();
        return $this->response->response($this->mailService->sendCode($target,"NFLS.IO Email Verification","The verification code for ".$readableAction." is ".$this->token));
    }
}
