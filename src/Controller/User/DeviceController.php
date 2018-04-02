<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\OAuth\Client;
use App\Entity\Preference;
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
}
