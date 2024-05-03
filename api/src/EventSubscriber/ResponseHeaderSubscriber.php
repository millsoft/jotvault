<?php

namespace App\EventSubscriber;

use App\Utils\HttpHeaderConstants;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseHeaderSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', 0],
        ];
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        //check if we have session, if yes, set the X-Session-Id header:
        if ($request->hasSession()) {
            $session = $request->getSession();
            $sessionId = $session->getId();
            $response->headers->set(HttpHeaderConstants::SESSION_ID, $sessionId);
        }
    }
}
