<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\User\Chat;
use App\Entity\User\User;
use App\Model\Permission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChatController extends AbstractController
{

    /**
     * @Route("/chat/list", methods="GET")
     */
    public function list(Request $request)
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $this->updateUser();
        $list = $this->getDoctrine()->getManager()->getRepository(Chat::class)->list(
            $this->getUser(),
            $request->query->getInt("page", 1)
        );
        foreach ($list as $message) {
            /** @var $message Chat */
            $message->canReply = $message->getSender() !== $this->getUser();
        }
        return $this->response()->responseEntity(
            $list
        );
    }

    private function updateUser()
    {
        $this->getUser()->setReadTime(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($this->getUser());
        $em->flush();
    }


    /**
     * @Route("/chat/send", methods="POST")
     */
    public function send(Request $request)
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $this->denyAccessUnlessGranted(Permission::IS_AUTHENTICATED);
        $this->verfityCsrfToken($request->request->get("_csrf"),AbstractController::CSRF_USER);
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find(
            $request->request->getInt("id")
        );
        if (is_null($user))
            return $this->response()->response("收件人不存在！", Response::HTTP_FORBIDDEN);
        $chat = new Chat();
        $chat->setSender($this->getUser());
        $chat->setReceiver($user);
        if($chat->getReceiver() === $chat->getSender())
            return $this->response()->response("不能给自己发信息！",Response::HTTP_FORBIDDEN);
        $chat->setContent($request->request->get("content"));
        $em = $this->getDoctrine()->getManager();
        $em->persist($chat);
        $em->flush();
        return $this->response()->response(null);
    }
}
