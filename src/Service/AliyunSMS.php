<?php

namespace App\Servce;

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

class AlyunSMS {

    const REGISTER = "SMS_119080912";
    const RECOVER = "SMS_119085891";
    const BIND = "SMS_119085892";

    const PRODUCT = "Dysmsapi";
    const API_URL = "dysmsapi.aliyuncs.com";
    const REGION = "cn-hangzhou";

    const KEY_ID = "LTAIP9SuQgddEG0f";
    const KEY_SECRET = "HjWeMlsrGgEXunUSkJ54fBowbIqhf3";

    const SIGN_NAME = "南外人";

    /**
     * @var DefaultAcsClient
     */
    private $acsClient;

    public function __construct()
    {
        Config::load();
        $profile = DefaultProfile::getProfile(self::REGION, self::KEY_ID, self::KEY_SECRET);
        DefaultProfile::addEndpoint(self::REGION, self::REGION, self::PRODUCT, self::API_URL);
        $this->acsClient = new DefaultAcsClient($profile);
    }

    public function sendCode($code,$phone,$type){
        $request = new SendSmsRequest();
        $request->setPhoneNumbers($phone);
        $request->setSignName(self::SIGN_NAME);
        $request->setTemplateCode($type);
        $request->setTemplateParam(json_encode(array("code"=>(string)$code), JSON_UNESCAPED_UNICODE));
        $this->acsClient->getAcsResponse($request);
        return;
    }
}