<?php
/**
 * Created by PhpStorm.
 * User: hqy
 * Date: 2018/7/20
 * Time: 2:18 PM
 */

namespace App\Service;


use App\Entity\School\Alumni;
use App\Entity\User\User;
use Predis\Client;

class CacheService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client('tcp://127.0.0.1:6379');
    }

    public function antiSpiderWrite(User $user, ?array $users, ?array $ids){
        if(is_null($ids)) {
            $ids = array_map(function($object){
                /** @var Alumni $object */
                return $object->getUser()->getId();
            }, $users);
        }
        $this->client->set($this->getIdentifierForAntiSpider($user), json_encode($ids));
        $this->client->expire($this->getIdentifierForAntiSpider($user), 300);
    }

    public function antiSpiderUse(User $user, int $target) {
        $ids = json_decode($this->client->get($this->getIdentifierForAntiSpider($user)), true) ?? [];
        if (in_array($target,$ids)) {
            $ids = array_diff($ids, [$target]);
            $this->antiSpiderWrite($user, null, $ids);
            return true;
        }
        else {
            return false;
        }
    }

    public function canSend(string $ip, string $target) {
        $current = (int)$this->client->get($this->getIdentifierForCode($ip));
        if($current > 5)
            return false;
        $current ++;
        $this->client->set($this->getIdentifierForCode($ip), $current);
        $this->client->expire($this->getIdentifierForCode($ip), 1800);

        $current = (int)$this->client->get($this->getIdentifierForCode($target));
        if($current > 1)
            return false;
        $current ++;
        $this->client->set($this->getIdentifierForCode($target), $current);
        $this->client->expire($this->getIdentifierForCode($target), 60);
        return true;
    }

    public function rateWrite(User $user) {
        $current = (int)$this->client->get($this->getIdentifierForRate($user));
        $current ++;
        $this->client->set($this->getIdentifierForRate($user), $current);
        $this->client->expire($this->getIdentifierForRate($user), 600);
        return $current;
    }

    public function rateVerify(User $user) {
        $current = (int)$this->client->get($this->getIdentifierForRate($user));
        if($current < 5)
            return true;
        else
            return false;
    }

    public function codeWrite(string $target, string $code, int $action) {
        $this->client->set($target, json_encode(["code" => $code, "action" => $action]));
        $this->client->expire($target, 600);
    }

    public function codeVerify(string $target, string $code, int $action) {
        $verification = json_decode($this->client->get($target), true);
        if(is_null($verification))
            return false;
        if($verification["code"] == $code && $verification["action"] == $action) {
            $this->client->set($target, null);
            $this->client->expire($target, 1);
            return true;
        }
        else {
            return false;
        }
    }

    private function getIdentifierForAntiSpider(User $user){
        return "antispider.".(string)$user->getId();
    }

    private function getIdentifierForCode(string $target){
        return "code.".$target;
    }

    private function getIdentifierForRate(User $user) {
        return "rate.".(string)$user->getId();
    }
}