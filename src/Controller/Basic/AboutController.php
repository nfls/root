<?php

namespace App\Controller\Basic;

use App\Controller\AbstractController;
use App\Entity\Preference;
use App\Entity\User\Device;
use App\Service\APNSService;
use App\Service\CeleryService;
use Longman\TelegramBot\Telegram;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class AboutController extends AbstractController
{
    /**
     * @Route("/about/devs", name="about")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Preference::class);
        return $this->response()->response($repo->get(Preference::ABOUT_DEVS));
    }

    /**
     * @Route("/about/version")
     */
    public function version()
    {
        exec('git lg2 -10', $gitHashLong);
        $gitHashLong = array_reduce($gitHashLong, function ($previous, $current) {
            return $previous . "<br/>" . $current;
        });
        return $this->response()->response($gitHashLong);
    }

    /**
     * @Route("about/test")
     */
    public function test(APNSService $service) {
        $device = $this->getDoctrine()->getManager()->getRepository(Device::class)->find("f7016e4b-44f1-439f-904a-05be55ef7d0e");
        $service->push($device,"测试","这是副标题","这是内容这是内容这是内容这是内容这是内容这是内容",1,null,"https://nfls.io/uploads/d33a39bc90779f289bc8a3276431d068.jpeg");

    }

}
