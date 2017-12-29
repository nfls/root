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
    public function send($phone){
        $this->client->verify([
            'number' => (string)$phone
            //'bran'
        ]);
    }
}