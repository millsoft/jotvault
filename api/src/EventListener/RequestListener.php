<?php

namespace App\EventListener;

use App\Services\EncryptionKeyManager;
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

        if ($request->headers->has('X-Encryption-Key')) {
            $this->encryptionKeyManager->setKey($request->headers->get('X-Encryption-Key'));
        }

    }
}
