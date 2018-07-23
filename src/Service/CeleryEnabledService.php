<?php
/**
 * Created by PhpStorm.
 * User: hqy
 * Date: 2018/7/22
 * Time: 2:06 PM
 */

namespace App\Service;


use Doctrine\Common\Persistence\ObjectManager;

class CeleryEnabledService
{
    protected $celery;

    protected $objectManager;

    public function __construct(CeleryService $celeryService, ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->celery = $celeryService;
    }
}