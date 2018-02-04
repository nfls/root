<?php
namespace App\Service;
use Doctrine\ORM\EntityManager;
use Nexmo;

class NexmoSMS {
    private $client;

    private $key_id;
    private $key_secret;

    public function __construct()
    {
        $this->key_id = $_SERVER["NEXMO_KEY_ID"];
        $this->key_secret = $_SERVER["NEXMO_KEY_SECRET"];

        $this->client = new Nexmo\Client(new Nexmo\Client\Credentials\Basic($this->key_id, $this->key_secret));
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
        try{
            $verification = new Nexmo\Verify\Verification($id);
            $result = $this->client->verify()->check($verification,$code);
            if($result->getStatus() == 0){
                return true;
            }else{
                return false;
            }
        }catch (Nexmo\Client\Exception\Exception $e){
            var_dump($e->getMessage());
            return false;
        }
    }
    public function cancel($id){
        try{
            $verification = new Nexmo\Verify\Verification($id);
            $this->client->verify()->cancel($verification);
        }catch (Nexmo\Client\Exception\Exception $e){

        }

    }
    public function trigger($id){
        $verification = new Nexmo\Verify\Verification($id);
        $this->client->verify()->trigger($verification);
    }

}