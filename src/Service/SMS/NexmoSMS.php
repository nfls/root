<?php
namespace App\Service;
use App\Service\SMS\AbstractSmsService;
use Doctrine\ORM\EntityManager;
use libphonenumber\PhoneNumber;
use Nexmo;

class NexmoSMS extends AbstractSmsService {

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
        return;
    }

    public function sendDeadlineReminder(PhoneNumber $phone, string $teacher, string $project, string $time)
    {
        return;
    }

    public function verify(PhoneNumber $phone, string $code, string $id)
    {
        // TODO: Implement verify() method.
    }


}