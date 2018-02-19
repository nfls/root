<?php
namespace App\Service\Notification;

use App\Service\Notification\Provider\AliyunNotification;
use App\Service\Notification\Provider\MailNotification;
use App\Service\Notification\Provider\NexmoNotification;
use libphonenumber\PhoneNumber;
use Predis\Client;

class NotificationService {
    /**
     * @var $redis Client
     */
    private $redis;

    const ACTION_REGISTERING = 0;
    const ACTION_RESET = 1;
    const ACTION_BIND = 2;

    public function set(Client $client)
    {
        $this->redis = $client;
    }

    public function code($target,$action){
        $client = $this->getClient($target);
        if($this->redis->exists($client->getIdentifier($target))){
            $this->redis->del([$client->getIdentifier($target)]);
        }
        switch($action){
            case self::ACTION_REGISTERING:
                $code = $client->sendRegistration($target);
                break;
            case self::ACTION_BIND:
                $code = $client->sendBind($target);
                break;
            case self::ACTION_RESET:
                $code = $client->sendReset($target);
                break;
            default:
                return false;
        }
        if(!is_null($code)){
            $this->redis->set($client->getIdentifier($target),json_encode(array(
                "action" => $action,
                "rsp" => $code
            )));
            $this->redis->expire($client->getIdentifier($target),600);
            return true;
        } else {
            return false;
        }
    }

    public function verify($target,$code,$action){
        $client = $this->getClient($target);
        if($this->redis->exists($client->getIdentifier($target))){
            $ticket = json_decode($this->redis->get($client->getIdentifier($target)),true);
            if($ticket["action"] != $action)
                return false;
            if($client->verify($target,$code,$ticket)){
                $this->redis->del([$client->getIdentifier($target)]);
                return true;
            }else{
                return false;
            }
        }
        return false;
    }

    /**
     * @param $target
     * @return AliyunNotification|MailNotification|NexmoNotification
     */
    private function getClient($target){
        if($target instanceof PhoneNumber){
            if($target->getCountryCode() == 86){
                return new AliyunNotification();
            }else{
                return new NexmoNotification();
            }
        }else{
            return new MailNotification();
        }
    }

}