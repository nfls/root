<?php

namespace App\Controller\School;

use App\Controller\AbstractController;
use App\Entity\School\Claz;
use App\Entity\School\Notice;
use App\Entity\User\User;
use App\Model\Permission;
use App\Service\AliyunOSS;
use App\Service\Notification\Provider\AbstractNotificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class BlackBoardController extends AbstractController
{
    /**
     * @Route("/school/blackboard/list",methods="GET")
     */
    public function list(Request $request)
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);

        return $this->response()->responseEntity($this->getUser()->getClasses()->map(function ($val) {
            /** @var $val Claz */
            return array(
                "id" => $val->getId(),
                "title" => $val->getTitle()
            );
        }));
    }

    /**
     * @Route("/school/blackboard/detail", methods="GET")
     */
    public function detail(Request $request, TranslatorInterface $translator)
    {
        $id = $request->query->get("id");
        $repo = $this->getDoctrine()->getManager()->getRepository(Claz::class);
        /** @var $class  Claz */
        if(!$this->isValidUuid($id))
            return $this->response()->response($translator->trans("illegal-blackboard-id"),Response::HTTP_FORBIDDEN);
        $class = $repo->find($id);

        if (!is_null($class) && ($class->getStudents()->contains($this->getUser()) || $this->getUser()->hasRole(Permission::IS_ADMIN))) {
            if ($class->getTeacher() === $this->getUser() || $this->getUser()->hasRole(Permission::IS_ADMIN))
                $class->admin = true;
            if (!$class->admin)
                $class->removeFuture();
            if (!$request->query->has("page")) {
                return $this->response()->responseEntity($class);
            } else {
                return $this->response()->responseEntity($class->nextNotices($request->query->getInt("page", 0)));
            }
        } else {
            return $this->response()->response($translator->trans("blackboard-id-not-exists"),Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @Route("/school/blackboard/create", methods="POST")
     */
    public function create(Request $request, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $this->verfityCsrfToken($request->request->get("_csrf"),AbstractController::CSRF_SCHOOL_BLACKBOARD);
        if (!$this->getUser()->hasRole(Permission::IS_ADMIN) && !$this->getUser()->hasRole(Permission::IS_TEACHER))
            throw $this->createAccessDeniedException();
        $class = new Claz();
        if(!is_string($request->request->get("title")))
            return $this->response()->response($translator->trans("blackboard-blank-name"),Response::HTTP_BAD_REQUEST);
        $class->setTitle($request->request->get("title"));
        $class->setTeacher($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($class);
        $em->persist($this->getUser());
        $em->flush();
        return $this->response()->response(null);
    }


    /**
     * @Route("/school/blackboard/edit", methods="POST")
     */
    public function edit(Request $request,TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $this->verfityCsrfToken($request->request->get("_csrf"),AbstractController::CSRF_SCHOOL_BLACKBOARD);
        if (!$this->getUser()->hasRole(Permission::IS_ADMIN) && !$this->getUser()->hasRole(Permission::IS_TEACHER))
            throw $this->createAccessDeniedException();
        $id = $request->query->get("id");
        $repo = $this->getDoctrine()->getManager()->getRepository(Claz::class);
        /** @var $class  Claz */
        $class = $repo->findOneBy(["id" => $id]);
        if (!is_null($class) & ($class->getTeacher() === $this->getUser() || $this->getUser()->hasRole(Permission::IS_ADMIN))) {
            $uid = $request->request->get("id");
            $userRepo = $this->getDoctrine()->getManager()->getRepository(User::class);
            $user = $userRepo->find($uid);
            if ($request->request->has("add"))
                $class->addStudent($user);
            else if ($request->request->has("remove"))
                $class->removeStudent($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($class);
            $em->flush();
            return $this->response()->response(null);
        } else {
            return $this->response()->response($translator->trans("blackboard-id-not-exists"),Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @Route("/school/blackboard/delete", methods="POST")
     */
    public function delete(Request $request, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $this->verfityCsrfToken($request->request->get("_csrf"),AbstractController::CSRF_SCHOOL_BLACKBOARD);
        if (!$this->getUser()->hasRole(Permission::IS_ADMIN) && !$this->getUser()->hasRole(Permission::IS_TEACHER))
            throw $this->createAccessDeniedException();
        $id = $request->query->get("id");
        $repo = $this->getDoctrine()->getManager()->getRepository(Claz::class);
        /** @var $class  Claz */
        $class = $repo->findOneBy(["id" => $id]);
        if (!is_null($class) & ($class->getTeacher() === $this->getUser() || $this->getUser()->hasRole(Permission::IS_ADMIN))) {
            $uid = $request->request->get("id");
            $noticeRepo = $this->getDoctrine()->getManager()->getRepository(Notice::class);
            $notice = $noticeRepo->find($uid);
            $class->removeNotice($notice);
            $em = $this->getDoctrine()->getManager();
            $em->remove($notice);
            $em->persist($class);
            $em->flush();
            return $this->response()->response(null);
        } else {
            return $this->response()->response($translator->trans("blackboard-id-not-exists"),Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @Route("/school/blackboard/preference", methods="POST")
     */
    public function preference(Request $request, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $this->verfityCsrfToken($request->request->get("_csrf"),AbstractController::CSRF_SCHOOL_BLACKBOARD);
        if (!$this->getUser()->hasRole(Permission::IS_ADMIN) && !$this->getUser()->hasRole(Permission::IS_TEACHER))
            throw $this->createAccessDeniedException();
        $id = $request->query->get("id");
        $repo = $this->getDoctrine()->getManager()->getRepository(Claz::class);
        /** @var $class  Claz */
        $class = $repo->findOneBy(["id" => $id]);
        if (!is_null($class) & ($class->getTeacher() === $this->getUser() || $this->getUser()->hasRole(Permission::IS_ADMIN))) {
            $em = $this->getDoctrine()->getManager();
            if ($request->request->has("delete")) {
                foreach ($class->getNotices() as $val) {
                    $em->remove($val);
                }
                foreach ($class->getStudents() as $val) {
                    /** @var $val User */
                    $val->removeClass($class);
                    $em->persist($val);
                }
                $em->remove($class);
            } else {
                $class->setAnnouncement($request->request->get("announcement"));
                $class->setTitle($request->request->get("title"));
                $em->persist($class);
            }

            $em->flush();
            return $this->response()->response(null);
        } else {
            return $this->response()->response($translator->trans("blackboard-id-not-exists"),Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @Route("/school/blackboard/signature", methods="GET")
     */
    public function signature(Request $request)
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        if (!$this->getUser()->hasRole(Permission::IS_ADMIN) && !$this->getUser()->hasRole(Permission::IS_TEACHER))
            throw $this->createAccessDeniedException();
        $oss = new AliyunOSS();
        return $this->response()->response($oss->privateUploadSignature($request->query->get("object"), $request->query->get("type")));
    }

    /**
     * @Route("/school/blackboard/post", methods="POST")
     */
    public function post(Request $request, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $this->verfityCsrfToken($request->request->get("_csrf"),AbstractController::CSRF_SCHOOL_BLACKBOARD);
        if (!$this->getUser()->hasRole(Permission::IS_ADMIN) && !$this->getUser()->hasRole(Permission::IS_TEACHER))
            throw $this->createAccessDeniedException();
        $id = $request->query->get("id");
        $repo = $this->getDoctrine()->getManager()->getRepository(Claz::class);
        /** @var $class  Claz */
        $class = $repo->findOneBy(["id" => $id]);

        if (!is_null($class) & ($class->getTeacher() === $this->getUser() || $this->getUser()->hasRole(Permission::IS_ADMIN))) {
            $notice = new Notice();
            if(is_null($request->request->get("content")))
                return $this->response()->response($translator->trans("content-blank"),Response::HTTP_FORBIDDEN);
            $notice->setContent($request->request->get("content"));
            $notice->setAttachment($request->request->get("files"));
            $notice->setClaz($class);

            $ddl = \DateTime::createFromFormat("Y-m-d\TH:i:s\.???\Z", $request->request->get("deadline"), new \DateTimeZone("UTC"));
            if ($ddl === false)
                $ddl = null;
            else {
                $ddl->setTimezone(new \DateTimeZone($request->request->get("timezone")));
                if ($ddl >= new \DateTime()){
                    $notice->setDeadline($ddl);
                    $notice->setTitle($request->request->get("title"));
                }
            }


            $time = \DateTime::createFromFormat("Y-m-d\TH:i:s\.???\Z", $request->request->get("time"), new \DateTimeZone("UTC"));
            if ($time === false) {
                $time = new \DateTime();
            } else {
                $time->setTimezone(new \DateTimeZone($request->request->get("timezone")));
                if ($time < new \DateTime())
                    $time = new \DateTime();
            }
            $notice->setTime($time);
            $em = $this->getDoctrine()->getManager();
            $em->persist($notice);
            $em->flush();
            if($time <= new \DateTime())
                $this->notification()->notifyNewNotice($class,$notice);
            return $this->response()->response(null);
        } else {
            return $this->response()->response($translator->trans("blackboard-id-not-exists"),Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @Route("/school/blackboard/notify", methods="POST")
     */
    public function notify(Request $request, TranslatorInterface $translator){
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        $this->verfityCsrfToken($request->request->get("_csrf"),AbstractController::CSRF_SCHOOL_BLACKBOARD);
        if (!$this->getUser()->hasRole(Permission::IS_ADMIN) && !$this->getUser()->hasRole(Permission::IS_TEACHER))
            throw $this->createAccessDeniedException();
        if(!$this->verifyCaptcha($request->request->get("captcha")))
            return $this->response()->response($translator->trans("incorrect-captcha"), Response::HTTP_UNAUTHORIZED);
        $id = $request->query->get("id");
        $classRepo = $this->getDoctrine()->getManager()->getRepository(Claz::class);
        /** @var $class  Claz */
        $class = $classRepo->find($id);
        $noticeRepo = $this->getDoctrine()->getManager()->getRepository(Notice::class);
        $notice = $noticeRepo->find($request->request->get("id"));
        if(!is_null($notice->getDeadline()) && $notice->claz() === $class)
        {
            $this->notification()->notifyDeadline($class,$notice);
        }else {
            return $this->response()->response($translator->trans("cannot-send-notice"),Response::HTTP_FORBIDDEN);
        }
        return $this->response()->response(null);
    }

    /**
     * @Route("/school/blackboard/eligibility", methods="GET")
     */
    public function eligibility()
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        return $this->response()->response(($this->getUser()->hasRole(Permission::IS_ADMIN) || ($this->getUser()->hasRole(Permission::IS_TEACHER))));
    }
}