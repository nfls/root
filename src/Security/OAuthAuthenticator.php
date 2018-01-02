<?php

namespace App\Security;

use App\Controller\OAuthController;
use Doctrine\ORM\EntityManager;
use League\OAuth2\Server\AuthorizationServer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class OAuthAuthenticator extends AbstractGuardAuthenticator
{

    private $server;

    public function __construct()
    {
        $controller = new OAuthController();
        $controller->init();
        $server = $controller->server;
    }

    public function supports(Request $request)
    {
        return ($request->headers->has("Authorization") || $request->query->has("access_token"));
    }

    public function getCredentials(Request $request)
    {


        // todo
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // todo
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // todo
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // todo
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // todo
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // todo
    }

    public function supportsRememberMe()
    {
        // todo
    }
}
