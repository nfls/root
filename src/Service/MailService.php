<?php
/**
 * Created by PhpStorm.
 * User: hqy
 * Date: 2018/7/22
 * Time: 2:03 PM
 */

namespace App\Service;


class MailService extends CeleryEnabledService
{
    public function send(string $sender, string $senderName, string $receiver, string $subject, string $content) {
        try {
            $this->celery->client->PostTask("tasks.sendEmail", array(
                $sender,
                $senderName,
                $receiver,
                $subject,
                $content,
                "text/html"
            ));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function bulk(string $sender, string $senderName, array $receivers, string $subject, string $content) {
        foreach ($receivers as $receiver) {
            $this->send($sender, $senderName, $receiver, $subject, $content);
        }
        return true;
    }
}