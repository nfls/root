<?php

namespace App\Security;

use App\Entity\User\User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class CookieAuthenticator extends AbstractGuardAuthenticator
{

    public function supports(Request $request)
    {
        return $request->cookies->has("remember_token");
    }

    public function getCredentials(Request $request)
    {
        return $request->cookies->get("remember_token");
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials);
    }

    /**
     * @param mixed $credentials
     * @param User $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $user->getToken() === $credentials;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->invalidate();
        $response = RedirectResponse::create("/");
        $time = new \DateTime();
        $time->sub(new \DateInterval("P1M"));
        $response->headers->setCookie(new Cookie("remember_token", "deleted", $time, "/", null, false, true));
        $response->headers->setCookie(new Cookie("PHPSESSID", "deleted", $time, "/", null, false, true));
        return $response;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $session = $request->getSession() ?? new Session();
        $session->start();
        $session->set("user_token", $request->cookies->get("remember_token"));
        return;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return false;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
