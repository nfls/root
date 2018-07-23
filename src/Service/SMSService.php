<?php
/**
 * Created by PhpStorm.
 * User: hqy
 * Date: 2018/7/22
 * Time: 5:49 PM
 */

namespace App\Service;


class SMSService extends CeleryEnabledService
{
    public function send(string $receiver, string $template, array $params) {
        try {
            $this->celery->client->PostTask("tasks.sendSMS",array($receiver, $template, $params));
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }
}