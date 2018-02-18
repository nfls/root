<?php
namespace App\Service;

use libphonenumber\PhoneNumber;
use Predis\Client;

class SMSService {
    /**
     * @var $redis Client
     */
    private $redis;



    private function storeCode(PhoneNumber $phone){

    }

    public function __construct(Client $client)
    {
        $this->redis = $client;
    }

}