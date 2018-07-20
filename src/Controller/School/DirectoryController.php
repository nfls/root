<?php

namespace App\Controller\School;

use App\Controller\AbstractController;
use App\Entity\School\Alumni;
use App\Model\Permission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DirectoryController extends AbstractController
{
    /**
     * @Route("/alumni/directory/search", methods="POST")
     */
    public function search(Request $request)
    {
        $this->denyAccessUnlessGranted(Permission::IS_AUTHENTICATED);
        $alumni = $this->getDoctrine()->getManager()->getRepository(Alumni::class)->directoryQuery($request);
        $user = array_map(function ($val) {
            /** @var $val Alumni */
            return $val->getUser();
        }, $alumni);
        $user = array_values(array_unique($user));
        return $this->response()->responseEntity($user);
    }

    /**
     * @Route("/alumni/directory/query", methods="POST")
     */
    public function query(Request $request)
    {
        $text = $request->request->get("name");
        $registration = $request->request->get("registration");
        $class = $request->request->get("class");
        $alumni = $this->getDoctrine()->getManager()->getRepository(Alumni::class)->search($text, $registration, $class);
        $user = array_map(function ($val) {
            /** @var $val Alumni */
            return $val->getUser();
        }, $alumni);
        $user = array_values(array_unique($user));
        return $this->response()->responseEntity($user);

    }
}
