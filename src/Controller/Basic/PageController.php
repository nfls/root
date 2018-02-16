<?php

namespace App\Controller\Basic;

use App\Controller\AbstractController;
use App\Entity\Preference;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;

class PageController extends AbstractController
{
    /**
     * @Route("/",name="index",methods="GET")
     */
    public function index()
    {
        if ($_ENV["APP_ENV"] == "dev")
            return $this->render("index.debug.html.twig");
        return $this->render("index.html.twig");
    }

    /**
     * @Route("/un")
     */
    public function un() {
        return $this->render("unsupported.html.twig");
    }
    /**
     * @Route("/device")
     */
    public function getBrowser(Request $request) {
        $dd = new DeviceDetector($request->headers->get("user-agent"));
        $dd->parse();
        dump($dd);
        return $this->response()->response(null);
    }

    /**
     * @Route("/message/announcement", methods="GET")
     */
    public function getAnnouncement(Request $request){
        return $this->response()->response($this->getDoctrine()->getRepository(Preference::class)->get(Preference::DASHBOARD_ANNOUNCEMENT));
    }
}
