<?php

namespace App\Service;

use Mrgoon\AliSms\AliSms;

class AliyunSMS {

    const REGISTER = "SMS_119080912";
    const RECOVER = "SMS_119085891";
    const BIND = "SMS_119085892";
    const UNBIND = "SMS_119090970";

    private $key_id;
    private $key_secret;

    const SIGN_NAME = "南外人";
    /**
     * @var AliSms
     */
    private $sms;
    /**
     * @var array
     */
    private $config;

    public function __construct()
    {
        $this->key_id = $_SERVER["ALIYUN_KEY_ID"];
        $this->key_secret = $_SERVER["ALIYUN_KEY_SECRET"];
        $this->sms = new AliSms();
        $this->config = [
            'access_key' => $this->key_id,
            'access_secret' => $this->key_secret,
            'sign_name' => self::SIGN_NAME
        ];
    }

    function sendCode($phone,$code,$type){
        $response = $this->sms->sendSms((string)$phone, $type, ['code'=> (string)$code], $this->config);
    }

}