<?php

namespace App\Controller;

use App\Entity\Log;
use App\Entity\Preference;
use App\Entity\User\User;
use App\Model\ApiResponse;
use App\Model\Permission;
use App\Service\Notification\NotificationService;
use App\Service\SettingService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Csrf\CsrfToken;

class AbstractController extends Controller
{

    const CSRF_USER = "user";
    const CSRF_ALUMNI_FORM = "alumni.form";
    const CSRF_MEDIA_GALLERY = "media.gallery";
    const CSRF_SCHOOL_BLACKBOARD = "school.blackboard";

    public function __construct()
    {
    }

    public function response()
    {
        if (is_null($this->getUser()))
            return new ApiResponse(false);
        else
            return new ApiResponse($this->getUser()->hasRole(Permission::IS_AUTHENTICATED));
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return parent::getUser();
    }

    public function setting()
    {
        return $this->getDoctrine()->getManager()->getRepository(Preference::class);
    }

    public function verifyCaptcha($captcha)
    {
        $verifyResponse = file_get_contents('https://www.recaptcha.net/recaptcha/api/siteverify?secret=' . $_ENV["GOOGLE_KEY"] . '&response=' . $captcha);
        if (json_decode($verifyResponse)->success) {
            return true;
        } else {
            return false;
        }
    }

    public function verfityCsrfToken($token, $id)
    {
        if (is_null($this->getUser()))
            throw $this->createAccessDeniedException("Csrf Invalid.");
        if (is_null($token))
            throw $this->createAccessDeniedException("Csrf Invalid.");
        else
            if ($this->getUser()->isOAuth) {
                return true;
            } else {
                /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrf */
                $csrf = $this->get('security.csrf.token_manager');
                if ($csrf->isTokenValid(new CsrfToken($id, $token)))
                    return true;
                else
                    throw $this->createAccessDeniedException("Csrf Invalid.");
            }
    }

    public function writeLog(string $identifier, ?string $message = null, ?User $user = null)
    {
        if (is_null($user))
            $user = $this->getUser();
        $log = new Log();
        $log->setUser($user);
        $log->setIdentifier($identifier);
        $log->setMessage($message);
        $em = $this->getDoctrine()->getManager();
        $em->persist($log);
        $em->flush();
    }

    public function isValidUuid($uuid)
    {
        if (is_null($uuid))
            return false;
        else if (preg_match('/[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}/', $uuid)) {
            return true;
        } else {
            return false;
        }
    }
}
