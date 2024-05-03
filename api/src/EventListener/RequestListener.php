<?php

namespace App\EventListener;

use App\Services\EncryptionKeyManager;
use App\Utils\HttpHeaderConstants;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Class RequestListener
 * a middleware to set the encryption key from the request headers
 */
class RequestListener
{
    private EncryptionKeyManager $encryptionKeyManager;

    public function __construct(EncryptionKeyManager $encryptionKeyManager)
    {
        $this->encryptionKeyManager = $encryptionKeyManager;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $session = $request->getSession();

        //check if we have a session id in the header
        if ($request->headers->has(HttpHeaderConstants::SESSION_ID)) {
            $session->setId($request->headers->get(HttpHeaderConstants::SESSION_ID));
        } else {
            $session->set("user_id", null);
        }

        if ($request->headers->has(HttpHeaderConstants::ENCRYPTION_KEY)) {
            $this->encryptionKeyManager->setKey($request->headers->get(HttpHeaderConstants::ENCRYPTION_KEY));
        }

    }
}
