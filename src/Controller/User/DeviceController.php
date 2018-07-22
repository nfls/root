<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\OAuth\Client;
use App\Entity\Preference;
use App\Entity\User\Device;
use App\Model\Permission;
use App\Type\DeviceType;
use function GuzzleHttp\default_ca_bundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DeviceController extends AbstractController
{
    /**
     * @Route("device/announcement/ios", methods="GET")
     */
    public function getHeader()
    {
        return $this->response()->response($this->setting()->get(Preference::IOS_ANNOUNCEMENT));
    }

    /**
     * @Route("device/version", methods="GET")
     */
    public function version(Request $request)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Client::class);
        $client = $repo->getClientById($request->query->get("client_id"));
        return $this->response()->response($client->getVersion());
    }

    /**
     * @Route("device/pushRegister", methods="POST")
     */
    public function register(Request $request)
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $token = $request->request->get("token");
        $em = $this->getDoctrine()->getManager();
        /** @var Device $device */
        $device = $em->getRepository(Device::class)->findOneByUserAndToken($this->getUser(), $token) ?? new Device();
        $device->setUser($this->getUser());
        $device->setToken($token);
        $device->setModel($request->request->get("model"));
        $type = $request->request->get("type");
        switch($type) {
            case "ios":
                $device->setType(DeviceType::IOS);
                break;
            case "wechat":
                $device->setType(DeviceType::WE_CHAT);
                break;
            default:
                throw new \InvalidArgumentException("Invalid type.");
        }
        $device->setTime();
        $em->persist($device);
        $em->flush();
        return $this->response()->responseEntity($device);
    }
}
