<?php

namespace App\Controller;

use App\Entity\Code;
use App\Entity\User;
use App\Model\ApiResponse;
use App\Service\AliyunSMS;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CodeController extends Controller
{
    /**
     * @var AliyunSMS
     */
    private $smsService;

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
    private $string;

    /**
     * CodeController constructor.
     */
    public function __construct()
    {
        $this->smsService = new AliyunSMS();
        $this->api = new ApiResponse();

        $this->code = mt_rand(100000, 999999);
    }

    /**
     * @Route("/code", name="code")
     */
    public function index()
    {

        // replace this line with your own code!
        return $this->render('@Maker/demoPage.html.twig', [ 'path' => str_replace($this->getParameter('kernel.project_dir').'/', '', __FILE__) ]);
    }

    /**
     * @Route("/code/phone/register", methods="POST", name="sendRegisterCode")
     */
    public function sendRegisterCode(Request $request){
        $phone = intval($request->request->get("phone"));
        $this->sendSMS($phone,"register",AliyunSMS::REGISTER);
        return $this->api->response(null,200);
    }

    /**
     * @Route("/code/phone/reset", methods="POST", name="sendResetCode")
     */
    public function sendRecoverCode(Request $request){
        $phone = intval($request->request->get("phone"));
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findByPhone($phone);
        if(is_null($user))
            return $this->api->response(null,400);
        $this->sendSMS($phone,"reset",AliyunSMS::RECOVER);
        return $this->api->response(null,200);
    }

    /**
     * @Route("/code/phone/change", methods="GET", name="sendChangeCode")
     */
    public function sendResetCode(Request $request){
        $phone = $this->getUser()->getPhone();
        if(is_null($phone)){
            return $this->api->response(null,400);
        }
        $phone = intval($request->request->get("phone"));
        $this->sendSMS($phone,"change",AliyunSMS::RECOVER);
        return $this->api->response(null,200);
    }

    /**
     * @Route("/code/phone/bind", methods="GET", name="sendBindCode")
     */
    public function sendBindCode(Request $request){
        $phone = $this->getUser()->getPhone();
        if(is_null($phone)){
            return $this->api->response(null,400);
        }
        $this->sendSMS($phone,"bind",AliyunSMS::BIND);
        return $this->api->response(null,200);
    }

    /**
     * @Route("/code/phone/unbind", methods="GET", name="sendUnBindCode")
     */
    public function sendUnBindCode(Request $request){
        $phone = $this->getUser()->getPhone();
        if(is_null($phone)){
            return $this->api->response(null,400);
        }
        $this->sendSMS($phone,"bind",AliyunSMS::UNBIND);
        return $this->api->response(null,200);
    }

    private function sendSMS($phone,$action,$type){
        $code = new Code();
        $code->setAction($action);
        $code->setCode((string)$this->code);
        $code->setDestination($phone);
        $code->setType("phone");
        $em = $this->getDoctrine()->getManager();
        $em->persist($code);
        $em->flush();
        $this->smsService->sendCode($phone,$this->code,$type);
    }
}
