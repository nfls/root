<?php

namespace App\Controller\Basic;

use App\Controller\AbstractController;
use App\Entity\Alumni;
use App\Entity\Preference;
use App\Model\Permission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/admin/preference")
     */
    public function getPreferenceList(Request $request)
    {
        $this->denyAccessUnlessGranted(Permission::IS_ADMIN);
        if ($request->request->has("identifier")) {
            $this->getDoctrine()->getManager()->getRepository(Preference::class)->set($request->request->get("identifier"), $request->request->get("content"));
            return $this->response()->response(null, 204);
        } else {
            return $this->response()->responseEntity($this->getDoctrine()->getManager()->getRepository(Preference::class)->findAll());
        }

    }

}
