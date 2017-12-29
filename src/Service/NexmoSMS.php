<?php
namespace App\Service;
use Nexmo;

class NexmoSMS {
    private $client;
    const API_KEY = "91490ea5";
    const API_SECRET = "cfaa6b8d6c8f365b";
    public function __construct()
    {
        $this->client = new Nexmo\Client(new Nexmo\Client\Credentials\Basic(self::API_KEY, self::API_SECRET));
    }
    public function send($phone,$brand){
        $verification = $this->client->verify()->start([
            'number' => (string)$phone,
            'code_length' => 6,
            'brand' => $brand
        ]);
        return $verification->getRequestId();
    }
    public function verify($id,$code){
        $verification = new Nexmo\Verify\Verification($id);
        $result = $this->client->verify()->check($verification,$code);
        if($result->getStatus() == 0){
            return true;
        }else{
            return false;
        }
    }
    public function cancel($id){
        $verification = new Nexmo\Verify\Verification($id);
        $this->client->verify()->cancel($verification);
    }
    public function trigger($id){
        $verification = new Nexmo\Verify\Verification($id);
        $this->client->verify()->trigger($verification);
    }
}