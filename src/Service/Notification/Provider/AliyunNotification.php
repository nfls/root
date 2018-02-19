<?php

namespace App\Service\Notification\Provider;

use App\Service\Notification\AbstractNotificationService;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use Mrgoon\AliSms\AliSms;
use Predis\Client;

class AliyunNotification extends AbstractNotificationService {

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
            'access_key' => $_SERVER["ALIYUN_KEY_ID"],
            'access_secret' => $_SERVER["ALIYUN_KEY_SECRET"],
            'sign_name' => self::SIGN_NAME
        ];
        parent::__construct();
    }

    private function sendCode(PhoneNumber $phone,string $template){
        $code = (string)mt_rand(100000, 999999);
        $this->sms->sendSms(
            $this->getDomesticNumber($phone),
            $template,
            ["code" => $code]
        );
        return $code;
    }

    public function sendRegistration(PhoneNumber $phone)
    {
        return $this->sendCode($phone, "SMS_119080912");
    }

    public function sendBind(PhoneNumber $phone)
    {
        return $this->sendCode($phone,"SMS_119085892");
    }

    public function sendReset(PhoneNumber $phone)
    {
        return $this->sendCode($phone, "SMS_119085891");
    }

    public function sendRealnameFailed(PhoneNumber $phone)
    {
        $this->sms->sendSms(
            $this->getDomesticNumber($phone),
            "SMS_125028515",
            []
        );
    }

    public function sendRealnameSucceeded(PhoneNumber $phone, string $status, string $expiry)
    {
        $this->sms->sendSms(
            $this->getDomesticNumber($phone),
            "SMS_125018524",
            [
                "status" => $status,
                "time" => $expiry
            ]
        );
    }

    public function sendNewMessage(PhoneNumber $phone)
    {
        $this->sms->sendSms(
            $this->getDomesticNumber($phone),
            "SMS_125028504",
            []
        );
    }

    public function sendNewNotice(PhoneNumber $phone, string $teacher, string $class)
    {
        $this->sms->sendSms(
            $this->getDomesticNumber($phone),
            "SMS_125018518",
            [
                "person" => $teacher,
                "group" => $class
            ]
        );
    }

    public function sendDeadlineReminder(PhoneNumber $phone, string $teacher, string $project, string $time)
    {
        $this->sms->sendSms(
            $this->getDomesticNumber($phone),
            "SMS_125028511",
            [
                "user" => $teacher,
                "project" => $project,
                "time" => $time
            ]
        );
    }

    public function verify(PhoneNumber $phone, string $code, array $ticket)
    {
        return $ticket["rsp"] == $code;
    }


}