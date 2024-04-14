<?php

namespace App\Entity;

interface IEncryptedNote
{
    public function getEncryptionKey(): ?string;
    public function setEncryptionKey(?string $encryptionKey): void;
}
