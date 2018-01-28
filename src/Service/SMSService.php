<?php
namespace App\Service;

use App\Entity\User\Code;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class SMSService {
    private $nexmoService;
    private $aliyunService;
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->nexmoService = new NexmoSMS();
        $this->aliyunService = new AliyunSMS();
        $this->em = $em;
    }

    public function validate($country,$phone,$code,$action){
        $util = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $phoneObject = $util->parse($phone,$country);
            $phoneE164 = $util->format($phoneObject,\libphonenumber\PhoneNumberFormat::E164);
            if($phoneObject->getCountryCode() == 86){
                $code = $this->em->getRepository(Code::class)->verifyCode($phone,$code,$action);

                if(is_null($code))
                    return false;
                $this->em->remove($code);

            } else {
                $codeId = $this->em->getRepository(Code::class)->getRequestId($phoneE164,$action);
                if(is_null($code))
                    return false;
                if(!$this->nexmoService->verify($codeId->getCode(),$code)){
                    return false;
                }
                $this->em->remove($codeId);
            }
            $this->em->flush();
            return $phoneE164;

        }catch(\libphonenumber\NumberParseException $e){
            return false;
        }
    }
}