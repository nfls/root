<?php
/**
 * Created by PhpStorm.
 * User: SYSTEM
 * Date: 2018/8/7
 * Time: 0:50
 */

namespace App\EventListener;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ResponseListener
{
    public function onKernelResponse(FilterResponseEvent $filterResponseEvent) {
        $response = $filterResponseEvent->getResponse();
        $request = $filterResponseEvent->getRequest();
        $client = $request->headers->get("Client");
        if($response instanceof RedirectResponse && ($client === "weChat" || $client === "iOS")) {
            $filterResponseEvent->setResponse(JsonResponse::create(["data"=>$response->getTargetUrl(), "code"=>Response::HTTP_TEMPORARY_REDIRECT]));
        }
    }
}