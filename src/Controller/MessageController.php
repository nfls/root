<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
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
     * @return JsonResponse
     * @Route("message/count", name="Message(Count)", methods="GET")
     */
    public function getUnreadCount(){
        $em = $this->getDoctrine()->getManager()->getRepository(Message::class);
        return $this->response->response($em->getUnreadCount($this->getUser()->getReadTime()),200);
    }

    // Below is for admin part.

    /**
     * @Route("admin/basic/message")
     */
    public function renderMessage(){
        return $this->render("admin/basic/message.html.twig");
    }

    /**
     * @Route("admin/basic/message/edit")
     */
    public function getAllMesssages(Request $request){
        $messageRepo = $this->getDoctrine()->getManager()->getRepository(Message::class);
        $page = $request->request->get("page") ?? 1;
        $rows = $request->request->get("rows") ?? 10;

        if($request->request->has("id")){
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get("id");
            $message = $messageRepo->findOneBy(["id"=>$id]) ?? new Message();
            if($request->request->get("delete") == "true"){
                $em->remove($message);
            }else{
                $userRepo = $this->getDoctrine()->getManager()->getRepository(User::class);
                $receiver = $userRepo->findOneBy(["id"=>$request->request->get("receiver")]);
                $message->setImage($request->request->get("image"));
                $message->setDetail($request->request->get("detail"));
                $message->setTitle($request->request->get("title"));
                $message->setType($request->request->get("type"));
                $message->setUrl($request->request->get("url"));
                $message->setReceiver($receiver);
                $em->persist($message);
            }
            $em->flush();
            return $this->response->response(null,200);
        }else{
            if($request->query->get("filter") > 0){
                $type = $request->query->get("filter");
            }else{
                $type = null;
            }
            $data = $messageRepo->getAllMessages($page,$rows,$type,null);
            $count = $messageRepo->getAllMessagesCount($page,$rows,$type,null);
            return $this->response->responseRowEntity($data,$count,200);
        }
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
