<?php

namespace App\Controller\Basic;

use App\Controller\AbstractController;
use App\Entity\Preference;
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
    public function test(CeleryService $celeryService) {
        $celeryService->send();

    }

}
