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
}