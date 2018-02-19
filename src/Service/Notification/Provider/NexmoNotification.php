<?php
namespace App\Service\Notification\Provider;
use Doctrine\ORM\EntityManager;
use libphonenumber\PhoneNumber;
use Nexmo;
use Predis\Client;

class NexmoNotification extends AbstractNotificationService {

    /**
     * @var Nexmo\Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Nexmo\Client(new Nexmo\Client\Credentials\Basic($_SERVER["NEXMO_KEY_ID"], $_SERVER["NEXMO_KEY_SECRET"]));
        parent::__construct();
    }
    public function sendCode(PhoneNumber $phone){
        $verification = $this->client->verify()->start([
            'number' => $this->getInternationalNumber($phone),
            'code_length' => 6,
            'brand' => "NFLS.IO"
        ]);
        return $verification->getRequestId();
    }

    public function sendRegistration(PhoneNumber $phone)
    {
        return $this->sendCode($phone);
    }

    public function sendBind(PhoneNumber $phone)
    {
        return $this->sendCode($phone);
    }

    public function sendReset(PhoneNumber $phone)
    {
        return $this->sendCode($phone);
    }

    public function sendRealnameFailed(PhoneNumber $phone)
    {
        $text = new Nexmo\Message\Text(
            $this->getInternationalNumber($phone),
            "NFLSIO",
            "用户您好，您的实名认证申请已被退回，请登录查看详情。"
        );
        try {
            $this->client->message()->send($text);
        } catch (Nexmo\Client\Exception\Exception $e){

        }

    }

    public function sendRealnameSucceeded(PhoneNumber $phone, string $status, string $expiry)
    {
        $text = new Nexmo\Message\Text(
            $this->getInternationalNumber($phone),
            "NFLSIO",
            "用户您好，您的实名认证已通过审核，当前身份为$status ，有效期至$expiry 。"
        );
        try {
            $this->client->message()->send($text);
        } catch (Nexmo\Client\Exception\Exception $e){

        }
    }

    public function sendNewMessage(PhoneNumber $phone)
    {
        $text = new Nexmo\Message\Text(
            $this->getInternationalNumber($phone),
            "NFLSIO",
            "您收到了一条新的私信，请登录查看详情。"
        );
        try {
            $this->client->message()->send($text);
        } catch (Nexmo\Client\Exception\Exception $e){

        }
    }

    public function sendNewNotice(PhoneNumber $phone, string $teacher, string $class)
    {
        return;
    }

    public function sendDeadlineReminder(PhoneNumber $phone, string $teacher, string $project, string $time)
    {
        return;
    }

    public function verify(PhoneNumber $phone, string $code, array $ticket)
    {
        $id = $ticket["rsp"];
        try{
            $verification = new Nexmo\Verify\Verification($id);
            $result = $this->client->verify()->check($verification,$code);

            if($result->getStatus() == 0){
                return true;
            }else{
                return false;
            }
        }catch (Nexmo\Client\Exception\Exception $e){
            return false;
        }
    }


}