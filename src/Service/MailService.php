<?php
namespace App\Service;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class MailService {
    private $service;

    public function __construct()
    {

        $this->service = new PHPMailer(true);
        //$this->service->SMTPDebug = 2;
        $this->service->isSMTP();
        $this->service->Host = "hk2.nfls.io";
        $this->service->SMTPAuth = true;
        $this->service->Username = "no-reply@nfls.io";
        $this->service->Password = "612e8e9f";
        $this->service->SMTPSecure = "tls";
    }

    public function sendCode($target,$title,$message){
        try {
            $this->service->setFrom("no-reply@nfls.io");
            $this->service->addAddress($target);
            $this->service->isHTML(false);
            $this->service->Subject = $title;
            $this->service->Body = $message;
            $this->service->send();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;

    }
}