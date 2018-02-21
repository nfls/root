<?php

namespace App\Service\Notification;

use App\Entity\School\Claz;
use App\Entity\School\Notice;
use App\Entity\User\Chat;
use App\Entity\User\User;
use App\Service\Notification\Provider\AliyunNotification;
use App\Service\Notification\Provider\MailNotification;
use App\Service\Notification\Provider\NexmoNotification;
use libphonenumber\PhoneNumber;
use Predis\Client;

class NotificationService
{
    const ACTION_REGISTERING = 0;
    const ACTION_RESET = 1;
    const ACTION_BIND = 2;

    /**
     * @var $redis Client
     */
    private $redis;

    public function set(Client $client)
    {
        $this->redis = $client;
    }

    public function code($target, $action)
    {
        $client = $this->getClient($target);
        if ($this->redis->exists($client->getIdentifier($target))) {
            $this->redis->del([$client->getIdentifier($target)]);
        }
        switch ($action) {
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
        if (!is_null($code)) {
            $this->redis->set($client->getIdentifier($target), json_encode(array(
                "action" => $action,
                "rsp" => $code
            )));
            $this->redis->expire($client->getIdentifier($target), 600);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $target
     * @return AliyunNotification|MailNotification|NexmoNotification
     */
    private function getClient($target)
    {
        if ($target instanceof PhoneNumber) {
            if ($target->getCountryCode() == 86) {
                return new AliyunNotification();
            } else {
                return new NexmoNotification();
            }
        } else {
            return new MailNotification();
        }
    }


    public function verify($target, $code, $action)
    {
        $client = $this->getClient($target);
        if ($this->redis->exists($client->getIdentifier($target))) {
            $ticket = json_decode($this->redis->get($client->getIdentifier($target)), true);
            if ($ticket["action"] != $action)
                return false;
            if ($client->verify($target, $code, $ticket)) {
                $this->redis->del([$client->getIdentifier($target)]);
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function notifyRealnamePassed(User $user, array $status, ?\DateTime $expiry)
    {
        if($expiry)
            $expiry = $expiry->format('Y-m-d');
        else
            $expiry = "无期限";
        if($user->getEmail())
            $this->getClient($user->getEmail())->sendRealnameSucceeded($user->getEmail(),$status["zh"],$status["en"],$expiry);
        if($user->getPhone())
            $this->getClient($user->getPhone())->sendRealnameSucceeded($user->getPhone(),$status["zh"],$expiry);
    }

    public function notifyRealnameFailed(User $user)
    {
        if($user->getEmail())
            $this->getClient($user->getEmail())->sendRealnameFailed($user->getEmail());
        if($user->getPhone())
            $this->getClient($user->getPhone())->sendRealnameFailed($user->getPhone());
    }

    public function sendNewMessage(Chat $message)
    {
        $user = $message->getReceiver();
        if($user->getEmail())
            $this->getClient($user->getEmail())->sendNewMessage($user->getEmail(),$message->getSender()->getUsername(),$message->getContent());
        if($user->getPhone())
            $this->getClient($user->getPhone())->sendNewMessage($user->getPhone());
    }

    public function notifyNewNotice(User $user, $teacher, Claz $class, Notice $notice)
    {
        if($user->getEmail())
            $this->getClient($user->getEmail())->sendNewNotice($user->getEmail(),$teacher,$class->getTitle(),$notice->getContent());
        if($user->getPhone())
            $this->getClient($user->getPhone())->sendNewNotice($user->getPhone(),$teacher,$class->getTitle());
    }

    public function notifyDeadline(User $user, string $teacher, Notice $notice){
        if($user->getEmail())
            $this->getClient($user->getEmail())->sendDeadlineReminder($user->getEmail(),$teacher,$notice->getTitle(),$notice->getDeadline()->format("Y-m-d H:i:s"),$notice->getContent());
        if($user->getPhone())
            $this->getClient($user->getPhone())->sendDeadlineReminder($user->getPhone(),$teacher,$notice->getTitle(),$notice->getDeadline()->format("Y-m-d H:i:s"));
    }

    //TODO Security setting changes
}