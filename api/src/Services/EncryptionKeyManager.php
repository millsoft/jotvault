<?php

namespace App\Services;

/**
 * Class EncryptionKeyManager
 * will be used to store the encryption key and pass it from controllers to the EncryptSubscriber
 */
class EncryptionKeyManager
{
    private ?string $key = null;

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(?string $key): void
    {
        $this->key = $key;
    }

}
