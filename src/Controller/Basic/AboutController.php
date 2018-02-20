<?php

namespace App\Controller\Basic;

use App\Controller\AbstractController;
use App\Entity\Preference;
use App\Entity\User\Feedback;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/about/feedback", methods="POST")
     */
    public function feedback(Request $request)
    {
        $content = $request->request->get("content");
        if (null === $content)
            return $this->response()->response(null, Response::HTTP_BAD_REQUEST);
        $contact = $request->request->get("contact");
        if (null === $contact)
            return $this->response()->response(null, Response::HTTP_BAD_REQUEST);
        $feedback = new Feedback();
        $feedback->setContent($content);
        $feedback->setContact($contact);
        $em = $this->getDoctrine()->getManager();
        $em->persist($feedback);
        $em->flush();
        return $this->response()->response(null, Response::HTTP_NO_CONTENT);
    }
}
