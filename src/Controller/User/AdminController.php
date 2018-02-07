<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\Alumni;
use App\Entity\Preference;
use App\Entity\User\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return new RedirectResponse("/admin/alumni/auth");
    }

    /**
     * @Route("/admin/preference", methods="GET")
     */
    public function getPreferenceList(Request $request)
    {
        return $this->response->responseEntity($this->getDoctrine()->getManager()->getRepository(Preference::class)->findAll());
    }

}
