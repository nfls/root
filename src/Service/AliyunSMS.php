<?php

namespace App\Service;

use Mrgoon\AliSms\AliSms;

class AliyunSMS {

    const REGISTER = "SMS_119080912";
    const RECOVER = "SMS_119085891";
    const BIND = "SMS_119085892";
    const UNBIND = "SMS_119090970";

    const KEY_ID = "LTAIP9SuQgddEG0f";
    const KEY_SECRET = "HjWeMlsrGgEXunUSkJ54fBowbIqhf3";
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
        $this->sms = new AliSms();
        $this->config = [
            'access_key' => self::KEY_ID,
            'access_secret' => self::KEY_SECRET,
            'sign_name' => self::SIGN_NAME
        ];
    }

    function sendCode($phone,$code,$type){
        $response = $this->sms->sendSms((string)$phone, $type, ['code'=> (string)$code], $this->config);
    }

}