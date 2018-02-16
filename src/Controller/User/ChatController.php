<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\User\Chat;
use App\Entity\User\User;
use App\Model\Permission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChatController extends AbstractController
{

    /**
     * @Route("/chat/inbox", methods="GET")
     */
    public function inbox(Request $request){
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $this->updateUser();
        return $this->response()->responseEntity(
            $this->getDoctrine()->getManager()->getRepository(Chat::class)->getInbox(
                $this->getUser(),
                $request->query->getInt("page",1)
            )
        );
    }


    /**
     * @Route("/chat/outbox", methods="GET")
     */
    public function outbox(Request $request){
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        return $this->response()->responseEntity(
            $this->getDoctrine()->getManager()->getRepository(Chat::class)->getOutbox(
                $this->getUser(),
                $request->query->getInt("page",1)
            )
        );
    }

    /**
     * @Route("/chat/send", methods="POST")
     */
    public function send(Request $request){
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $this->denyAccessUnlessGranted(Permission::IS_AUTHENTICATED);
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find(
            $request->request->getInt("id")
        );
        if(is_null($user))
            throw $this->createAccessDeniedException();
        $chat = new Chat();
        $chat->setSender($this->getUser());
        $chat->setReceiver($user);
        $chat->setContent($request->request->get("content"));
        $em = $this->getDoctrine()->getManager();
        $em->persist($chat);
        $em->flush();
        return $this->response()->response(null);
    }

    private function updateUser(){
        $this->getUser()->setReadTime(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($this->getUser());
        $em->flush();
    }
}
