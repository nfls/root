<?php

namespace App\Service\Notification\Provider;

use libphonenumber\PhoneNumber;
use Mrgoon\AliSms\AliSms;

class AliyunNotification extends AbstractNotificationService
{

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

    public function sendRegistration(PhoneNumber $phone)
    {
        return $this->sendCode($phone, "SMS_119080912");
    }

    private function sendCode(PhoneNumber $phone, string $template)
    {
        $code = (string)mt_rand(100000, 999999);
        $rsp = $this->sms->sendSms(
            $this->getDomesticNumber($phone),
            $template,
            ["code" => $code],
            $this->config
        );
        return $code;
    }

    public function sendBind(PhoneNumber $phone)
    {
        return $this->sendCode($phone, "SMS_119085892");
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
            [],
            $this->config
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
            ],
            $this->config
        );
    }

    public function sendNewMessage(PhoneNumber $phone)
    {
        $this->sms->sendSms(
            $this->getDomesticNumber($phone),
            "SMS_125028504",
            [],
            $this->config
        );
    }

    public function sendNewNotice(PhoneNumber $phone, string $teacher, string $class)
    {
        $class = mb_substr($class,0,20);
        $this->sms->sendSms(
            $this->getDomesticNumber($phone),
            "SMS_125018518",
            [
                "person" => $teacher,
                "group" => $class
            ],
            $this->config
        );
    }

    public function sendDeadlineReminder(PhoneNumber $phone, string $teacher, string $project, string $time)
    {
        $teacher = mb_substr($teacher,0,20);
        $project = mb_substr($project,0,20);
        $this->sms->sendSms(
            $this->getDomesticNumber($phone),
            "SMS_125028511",
            [
                "user" => $teacher,
                "project" => $project,
                "time" => $time
            ],
            $this->config
        );
    }

    public function verify(PhoneNumber $phone, string $code, array $ticket)
    {
        return $ticket["rsp"] == $code;
    }


}