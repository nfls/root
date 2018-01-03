<?php

namespace App\Controller;

use App\Entity\Message;
use App\Model\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Model\Message as MessageConstant;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class MessageController extends Controller
{
    private $response;
    public function __construct()
    {
        $this->response = new ApiResponse();
    }

    /**
     * @Route("/message", name="message")
     */
    public function index()
    {
        // replace this line with your own code!
        return $this->render('@Maker/demoPage.html.twig', [ 'path' => str_replace($this->getParameter('kernel.project_dir').'/', '', __FILE__) ]);
    }

    /**
     * @param $request Request
     * @return JsonResponse
     * @Route("/message/news", name="Message(News List)", methods="GET")
     */
    public function getNews(Request $request){
        $page = $request->query->get("page") ?? 1;
        return $this->response->response($this->getMessages(MessageConstant::NEWS,$page));
    }

    /**
     * @param $request Request
     * @return JsonResponse
     * @Route("/message/system", name="Message(System List)", methods="GET")
     */
    public function getSystemMessages(Request $request){
        $page = $request->query->get("page") ?? 1;
        return $this->response->response($this->getMessages(MessageConstant::SYSTEM_MESSAGE,$page));
    }

    /**
     * @Route("message/count", name="Message(Count)", methods="GET")
     */
    public function getUnreadCount(){
        $em = $this->getDoctrine()->getManager()->getRepository(Message::class);
        return $this->response->response($em->getUnreadCount($this->getUser()->getReadTime()),200);
    }

    /**
     * @param $section MessageConstant
     * @param $page int
     * @return mixed
     */

    private function getMessages($section,$page){
        if($section == MessageConstant::SYSTEM_MESSAGE){
            $user = $this->getUser();
            $user->setReadTime(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        $em = $this->getDoctrine()->getManager()->getRepository(Message::class);
        $data = $em->getMessages($this->getUser(),$section,$page);
        return array_map([$this,"mapMessage"],$data);
    }

    /**
     * @param $message array
     * @return array
     */

    function mapMessage($message){
        unset($message["group"]);
        return $message;
    }
}
