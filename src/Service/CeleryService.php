<?php
/**
 * Created by PhpStorm.
 * User: hqy
 * Date: 2018/7/21
 * Time: 9:15 PM
 */

namespace App\Service;


class CeleryService
{
    private $client;

    public function __construct()
    {
        $this->client = new \Celery(
            "localhost",
            "",
            "",
            0,
            'celery',
            'celery',
            6379,
            'redis'
        );
    }

    /**
     * @throws \CeleryException
     * @throws \CeleryPublishException
     */
    public function send() {
        $this->client->PostTask("tasks.sendAPN", array("0b52f091a987450d41e6f7169044ccd36dd849bd848dcf078df40e3568320edb",
            "顾平德",
            null,
            "顾平德女装",
            10,
            [
                "imageUrl"=> "https://nfls.io/uploads/d33a39bc90779f289bc8a3276431d068.jpeg"
            ]));
    }
}