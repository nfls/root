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
    public function send(string $sender, string $senderName, ?string $receiver, string $subject, string $content) {
        if(is_null($receiver))
            return false;
        $banDomain = ['chacuo', '027168', 'bccto', 'a7996', 'zv68', 'sohus', 'piaa',
            'deiie', 'zhewei88', '11163', 'svip520', 'ado0', 'haida-edu',
            'sian', 'jy5201', 'chaichuang', 'xtianx', 'zymuying', 'dayone',
            'tianfamh', 'zhaoyuanedu', 'cuirushi', '6gmu', 'yopmail',
            'mailinator', 'www.', '.cm', 'pp.com', 'loaoa', 'oiqas', 'dawin', 'instalapple', '+'];
        foreach ($banDomain as $od) {
            if (stripos($receiver, $od) !== false)
                return false;
        }
        $re = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
        if (!preg_match($re, $receiver)) {
            return false;
        }
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