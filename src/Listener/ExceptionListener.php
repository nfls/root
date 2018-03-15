<?php
namespace App\Listener;

use DeviceDetector\DeviceDetector;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if($_SERVER['APP_DEBUG'] ?? ('prod' !== ($_SERVER['APP_ENV'] ?? 'dev')))
            return;
        $exception = $event->getException();
        if($exception instanceof HttpExceptionInterface){
            $response = new JsonResponse(array(
                "code" => $exception->getStatusCode(),
                "data" => $exception->getMessage()
            ),$exception->getStatusCode());
        }else{
            $response = new JsonResponse(array(
                "code" => $exception->getCode()
            ),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $event->setResponse($response);
    }
}