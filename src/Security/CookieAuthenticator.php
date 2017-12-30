<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

class CookieAuthenticator extends AbstractGuardAuthenticator
{

    /**
     * @var Session
     */
    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

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

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $response = new JsonResponse(array("code"=>400,"message"=>"Login required with Cookie."),Response::HTTP_UNAUTHORIZED);
        $response->headers->clearCookie("remember_token");
        return $response;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $this->session->set("user_token",$request->cookies->get("remember_token"));
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
