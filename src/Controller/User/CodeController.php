<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\User\Code;
use App\Entity\User\User;
use App\Model\Permission;
use App\Service\Notification\NotificationService;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class CodeController extends AbstractController
{
    /**
     * @var NotificationService
     */
    private $service;

    /**
     * CodeController constructor.
     */
    public function __construct(NotificationService $notifcation)
    {
        $this->service = $notifcation;
    }

    /**
     * @Route("/code/available", methods="GET")
     */
    public function getAvailableRegion(Request $request)
    {
        return $this->response()->response(json_decode(file_get_contents($this->get('kernel')->getRootDir() . "/Files/Phone.json")));
    }

    /**
     * @Route("/code/register", methods="POST", name="sendRegisterCode")
     */
    public function sendRegisterCode(Request $request, TranslatorInterface $translator)
    {
        if (!$this->verifyCaptcha($request->request->get("captcha")))
            return $this->response()->response($translator->trans("incorrect-captcha"), Response::HTTP_UNAUTHORIZED);
        $target = $this->getTarget($request, true);
        if (is_null($target))
            return $this->response()->response($translator->trans("already-used"), Response::HTTP_BAD_REQUEST);
        if ($this->notification()->code($target, NotificationService::ACTION_REGISTERING))
            return $this->response()->response(null);
        else
            return $this->response()->response($translator->trans("not-expired"), Response::HTTP_BAD_REQUEST);
    }

    private function getTarget(Request $request, $checkUsed)
    {
        $phone = intval($request->request->get("phone"));
        if ($phone > 0) {
            $country = $request->request->get("country");
            $util = PhoneNumberUtil::getInstance();
            try {
                $phoneObject = $util->parse($phone, $country);
                $used = $this->checkUsedPhone($util->format($phoneObject, PhoneNumberFormat::E164));
                if ($checkUsed && $used) {
                    return null;
                } else if (!$checkUsed && !$used) {
                    return null;
                }
                return $phoneObject;
            } catch (NumberParseException $e) {
                return null;
            }
        } else {
            $email = $request->request->get("email");
            $used = $this->checkUsedEmail($email);
            if ($checkUsed && $used) {
                return null;
            } else if (!$checkUsed && !$used) {
                return null;
            }
            return $email;
        }
    }

    private function checkUsedPhone($phone)
    {
        $em = $this->getDoctrine()->getManager()->getRepository(User::class);
        if (@!is_null($em->findByPhone($phone))) {
            return true;
        } else {
            return false;
        }
    }

    private function checkUsedEmail($email)
    {
        $em = $this->getDoctrine()->getManager()->getRepository(User::class);
        if (@!is_null($em->findByEmail($email))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @Route("/code/reset", methods="POST", name="sendResetCode")
     */
    public function sendResetCode(Request $request, TranslatorInterface $translator)
    {
        if (!$this->verifyCaptcha($request->request->get("captcha")))
            return $this->response()->response($translator->trans("incorrect-captcha"), Response::HTTP_UNAUTHORIZED);
        $em = $this->getDoctrine()->getManager()->getRepository(User::class);
        $target = $this->getTarget($request, false);
        if (is_null($target))
            return $this->response()->response($translator->trans("already-used"), Response::HTTP_BAD_REQUEST);
        if($this->notification()->code($target, NotificationService::ACTION_RESET))
            return $this->response()->response(null);
        else
            return $this->response()->response($translator->trans("not-expired"), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/code/bind", methods="POST", name="sendBindCode")
     */
    public function sendBindCode(Request $request, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(Permission::IS_LOGIN);
        if (!$this->verifyCaptcha($request->request->get("captcha")))
            return $this->response()->response($translator->trans("incorrect-captcha"), Response::HTTP_UNAUTHORIZED);
        $target = $this->getTarget($request, true);
        if (is_null($target))
            return $this->response()->response($translator->trans("already-used"), Response::HTTP_BAD_REQUEST);
        if($this->notification()->code($target, NotificationService::ACTION_BIND))
            return $this->response()->response(null);
        else
            return $this->response()->response($translator->trans("not-expired"), Response::HTTP_BAD_REQUEST);
    }
}
