<?php
/**
 * Created by PhpStorm.
 * User: hqy
 * Date: 2018/7/22
 * Time: 12:14 PM
 */

namespace App\Service;


use App\Entity\User\Device;
use Doctrine\Common\Persistence\ObjectManager;

class APNSService
{
    private $celery;

    public function __construct(CeleryService $celeryService)
    {
        $this->celery = $celeryService;
    }

    /**
     * @param Device $device
     * @param null|string $title
     * @param null|string $subtitle
     * @param null|string $body
     * @param int|null $badge
     * @param null|string $image
     * @throws \CeleryException
     * @throws \CeleryPublishException
     */
    public function push(Device $device, ?string $title, ?string $subtitle, ?string $body, ?int $badge, ?string $link, ?string $image) {
        $this->celery->client->PostTask("tasks.sendAPN",
            array(
                $device->getToken(),
                $device->getCallbackToken(),
                $title,
                $subtitle,
                $body,
                $badge,
                [
                    "imageUrl"=> $image,
                    "link" => $link,
                    "callbackToken" => $device->getCallbackToken()
                ]));
    }

    public function bulk(array $devices, ?string $title, ?string $subtitle, ?string $body, ?int $badge, ?string $image) {

    }
}