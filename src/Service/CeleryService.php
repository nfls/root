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
    public $client;

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
}