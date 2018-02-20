<?php

namespace App\Service\Notification\Provider;

use libphonenumber\PhoneNumber;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailNotification
{
    private $mail;
    private $renderer;

    public function __construct()
    {
        $this->renderer = new MainConstant();
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = "hk2.nfls.io";
        $this->mail->SMTPAuth = true;
        $this->mail->Username = "no-reply@nfls.io";
        $this->mail->Password = $_ENV["MAIL_PASSWORD"];
        $this->mail->SMTPSecure = "tls";
    }

    public function sendRegistration(string $email)
    {
        $token = base64_encode(random_bytes(16));
        $this->send($email,
            "【NFLS.IO/南外人】账户注册 Account Registering",
            $this->renderer->renderCodePage(
                "注册新账户",
                "registering a new account",
                $token
            )
        );
        return $token;
    }

    public function send($target, $title, $message)
    {
        $banDomain = ['chacuo', '027168', 'bccto', 'a7996', 'zv68', 'sohus', 'piaa',
            'deiie', 'zhewei88', '11163', 'svip520', 'ado0', 'haida-edu',
            'sian', 'jy5201', 'chaichuang', 'xtianx', 'zymuying', 'dayone',
            'tianfamh', 'zhaoyuanedu', 'cuirushi', '6gmu', 'yopmail',
            'mailinator', 'www.', '.cm', 'pp.com', 'loaoa', 'oiqas', 'dawin', 'instalapple', '+'];
        foreach ($banDomain as $od) {
            if (stripos($target, $od) !== false)
                return null;
        }
        $re = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
        if (!preg_match($re, $target)) {
            return null;
        }
        try {
            $this->mail->setFrom("no-reply@nfls.io");
            $this->mail->addAddress($target);
            $this->mail->isHTML(true);
            $this->mail->Subject = "=?utf-8?B?" . base64_encode($title) . "?=";
            $this->mail->CharSet = "UTF-8";
            $this->mail->Body = $message;
            $this->mail->send();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;
    }

    public function sendBind(string $email)
    {
        $token = base64_encode(random_bytes(16));
        $this->send($email,
            "【NFLS.IO/南外人】邮箱绑定 Email Binding",
            $this->renderer->renderCodePage(
                "绑定当前邮箱",
                "binding this email with your account",
                $token
            )
        );
        return $token;
    }

    public function sendReset(string $email)
    {
        $token = base64_encode(random_bytes(16));
        $this->send($email,
            "【NFLS.IO/南外人】重置密码 Password Resetting",
            $this->renderer->renderCodePage(
                "重置账户密码",
                "resetting your password",
                $token
            )
        );
        return $token;
    }

    public function sendRealnameFailed(string $email)
    {
        $this->send($email,
            "【NFLS.IO/南外人】实名认证 Verification",
            $this->renderer->renderRealnameFailed()
        );
    }

    public function sendRealnameSucceeded(string $email, string $statusCN, string $statusEN, string $expiry)
    {
        $this->send($email,
            "【NFLS.IO/南外人】实名认证 Verification",
            $this->renderer->renderRealnameSucceeded($statusCN,$statusEN,$expiry)
        );
    }

    public function sendNewMessage(string $email, string $sender, string $content)
    {
        $this->send($email,
            "【NFLS.IO/南外人】私信 PM",
            $this->renderer->renderNewMessagePage($sender,$content)
        );
    }

    public function sendNewNotice(string $email, string $teacher, string $class, string $notice)
    {
        $this->send($email,
            "【NFLS.IO/南外人】New Notice",
            $this->renderer->renderNoticePage($teacher,$class,$notice)
        );
    }

    public function sendDeadlineReminder(string $email, string $teacher, string $project, string $time, string $notice)
    {
        $this->send($email,
            "【NFLS.IO/南外人】Deadline Reminder",
            $this->renderer->renderDeadlinePage($teacher,$project,$time,$notice));
    }

    public function verify(string $target, string $code, array $ticket)
    {
        return $ticket["rsp"] == $code;
    }

    public function getIdentifier(string $email)
    {
        return $email;
    }


}