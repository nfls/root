<?php

namespace App\Security;

use App\Entity\User\User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;


class SessionAuthenticator extends AbstractGuardAuthenticator
{

    public function supports(Request $request)
    {
        return /*$request->getSession() && */
            $request->getSession()->has("user_token");
    }

    public function getCredentials(Request $request)
    {
        $request->getSession()->start();
        return array(
            "token" => $request->getSession()->get("user_token"),
            "drop" => $request->cookies->get("drop")
            );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var User $user */
        $user = $userProvider->loadUserByUsername($credentials["token"]);
        if($credentials["drop"])
            $user->disableAdmin = true;
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $user->getToken() === $credentials["token"];
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
        return;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse(array("code" => 400, "message" => "Login required with Session."), Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
