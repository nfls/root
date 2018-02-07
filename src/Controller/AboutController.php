<?php

namespace App\Controller;

use App\Entity\Preference;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AboutController extends AbstractController
{
    /**
     * @Route("/about/devs", name="about")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Preference::class);
        return $this->response->response($repo->get(Preference::ABOUT_DEVS));
    }
}
