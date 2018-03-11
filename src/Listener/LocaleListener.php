<?php
namespace App\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LocaleListener {
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if($request->cookies->get("locale") === "zh")
            $request->setLocale("zh");
        else if ($request->cookies->get("locale") === "en")
            $request->setLocale("en");
        else if(strpos($request->headers->get("Accept-Language"),"zh") === false)
            $request->setLocale("en");
        else
            $request->setLocale("zh");
    }
}