<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LocaleListener {
//    public function __construct(RequestStack $requestStack, string $defaultLocale = 'en', RequestContextAwareInterface $router = null)
//    {
//        $this->defaultLocale = $defaultLocale;
//        $this->requestStack = $requestStack;
//        $this->router = $router;
//    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $languages = $request->getLanguages();
        if(!count($languages)) $languages[] = $request->getDefaultLocale();

        if(!$request->hasPreviousSession()) {
            $request->getSession()->set('_locale',$languages[0]);
        }

        $request->setLocale($request->getSession()->get('_locale',$languages[0]));
    }
}