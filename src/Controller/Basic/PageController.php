<?php

namespace App\Controller\Basic;

use App\Controller\AbstractController;
use App\Entity\Preference;
use DeviceDetector\DeviceDetector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class PageController extends AbstractController
{
    /**
     * @Route("/",name="index",methods="GET")
     */
    public function index(Request $request)
    {
        if ($_ENV["APP_ENV"] == "dev") {
            return $this->render("index.debug.html.twig");
        } else {
            if ($request->cookies->get("continue") == "true")
                return $this->render("index.html.twig");
            $dd = new DeviceDetector($request->headers->get("user-agent"));
            $dd->parse();
            if ($dd->getClient("name") == "Chrome" && version_compare($dd->getClient("version"), "50.0", ">="))
                return $this->render("index.html.twig");
            if ($dd->getClient("name") == "Chrome Mobile" && version_compare($dd->getClient("version"), "50.0", ">="))
                return $this->render("index.html.twig");
            if ($dd->getOs() == "iOS")
                return $this->render("index.html.twig");
            return $this->render("unsupported.html.twig");
        }

    }

    /**
     * @Route("/message/announcement", methods="GET")
     */
    public function getAnnouncement(Request $request)
    {
        return $this->response()->response($this->getDoctrine()->getRepository(Preference::class)->get(Preference::DASHBOARD_ANNOUNCEMENT));
    }
}
