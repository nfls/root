<?php
namespace App\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LocaleListener {
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if(strpos($request->headers->get("Accept-Language"),"zh") === false)
            $request->setLocale("en");
        else
            $request->setLocale("zh");
    }
}