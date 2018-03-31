<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\Preference;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DeviceController extends AbstractController
{
    /**
     * @Route("device/announcement/ios", methods="GET")
     */
    public function getHeader()
    {
        return $this->response()->response($this->setting()->get(Preference::IOS_ANNOUNCEMENT));
    }
}
