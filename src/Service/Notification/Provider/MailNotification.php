<?php
namespace App\Service\Notification\Provider;
use libphonenumber\PhoneNumber;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailNotification {
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

    public function send($target,$title,$message){
        try {
            $this->mail->setFrom("no-reply@nfls.io");
            $this->mail->addAddress($target);
            $this->mail->isHTML(true);
            $this->mail->Subject = $title;
            $this->mail->Body = $message;
            $this->mail->send();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;
    }

    public function sendRegistration(string $email)
    {
        $token = base64_encode(random_bytes(16));
        $this->send($email,
            "【NFLS.IO/南外人】账户注册 Account Registering",
            $this->renderer->renderCodePage(
                "使用当前邮箱注册馨账户",
                "registering a new account with this email",
                $token
            )
        );
        return $token;
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

    public function sendRealnameFailed(PhoneNumber $phone)
    {
        // TODO: Implement sendRealnameFailed() method.
    }

    public function sendRealnameSucceeded(PhoneNumber $phone, string $status, string $expiry)
    {
        // TODO: Implement sendRealnameSucceeded() method.
    }

    public function sendNewMessage(PhoneNumber $phone)
    {
        // TODO: Implement sendNewMessage() method.
    }

    public function sendNewNotice(PhoneNumber $phone, string $teacher, string $class)
    {
        // TODO: Implement sendNewNotice() method.
    }

    public function sendDeadlineReminder(PhoneNumber $phone, string $teacher, string $project, string $time)
    {
        // TODO: Implement sendDeadlineReminder() method.
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