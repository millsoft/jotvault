<?php

namespace SpecShaper\EncryptBundle\Encryptors;

use App\Services\EncryptionKeyManager;
use SpecShaper\EncryptBundle\Event\EncryptKeyEvent;
use SpecShaper\EncryptBundle\Event\EncryptKeyEvents;
use SpecShaper\EncryptBundle\Exception\EncryptException;
use SpecShaper\EncryptBundle\Subscribers\DoctrineEncryptSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class for OpenSSL encryption.
 *
 * @author Mark Ogilvie <mark.ogilvie@ogilvieconsulting.net>
 * @author Michael Milawski <mm@millsoft.de>
 */
class OpenSslEncryptor implements EncryptorInterface
{
    public const METHOD = 'aes-256-cbc';

    /**
     * Secret key stored in the .env file and passed via parameters in the Encryptor Factory.
     */
    private string $secretKey;

    /**
     * Dynamic salt value.
     */
    private string $salt = "";

    private EventDispatcherInterface $dispatcher;

    /**
     * OpenSslEncryptor constructor.
     */
    public function __construct(EventDispatcherInterface $dispatcher, EncryptionKeyManager $encryptionKeyManager)
    {
        $this->setSalt($encryptionKeyManager->getKey());
        $this->dispatcher = $dispatcher;
    }

    public function __toString(): string
    {
        return self::class.':'.self::METHOD;
    }

    public function setSecretKey(string $secretKey): void
    {
        $this->secretKey = $secretKey;
    }

    /**
     * @throws \Exception
     */
    public function encrypt(?string $data): ?string
    {
        // If not data return data (null)
        if (is_null($data)) {
            return null;
        }

        // If the value already has the suffix <ENC> then ignore.
        if (DoctrineEncryptSubscriberInterface::ENCRYPTED_SUFFIX === substr($data, -5)) {
            return $data;
        }

        $key = $this->getSaltedKey();

        // Create a cipher of the appropriate length for this method.
        $ivsize = openssl_cipher_iv_length(self::METHOD);
        $iv = openssl_random_pseudo_bytes($ivsize);

        // Create the encryption.
        $ciphertext = openssl_encrypt(
            $data,
            self::METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        // Prefix the encoded text with the iv and encode it to base 64. Append the encoded suffix.
        return base64_encode($iv.$ciphertext).DoctrineEncryptSubscriberInterface::ENCRYPTED_SUFFIX;
    }

    /**
     * @throws \Exception
     */
    public function decrypt(?string $data): ?string
    {
        // If the value is an object or null then ignore
        if (is_null($data)) {
            return null;
        }

        // If the value does not have the suffix <ENC> then ignore.
        if (DoctrineEncryptSubscriberInterface::ENCRYPTED_SUFFIX !== substr($data, -5)) {
            return $data;
        }

        $data = substr($data, 0, -5);

        // If the data was just <ENC> the return null;
        if (empty($data)) {
            return $data;
        }

        $key = $this->getSaltedKey();

        $data = base64_decode($data);

        $ivsize = openssl_cipher_iv_length(self::METHOD);
        $iv = mb_substr($data, 0, $ivsize, '8bit');
        $ciphertext = mb_substr($data, $ivsize, null, '8bit');

        return openssl_decrypt(
            $ciphertext,
            self::METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
    }

    /**
     * Get the secret key.
     *
     * Decode the parameters file base64 key.
     * Check that the key is 256 bit.
     *
     * @throws \Exception
     */
    protected function getSecretKey(): string
    {
        // Throw an event to allow encryption keys to be defined during runtime.
        $getKeyEvent = new EncryptKeyEvent();

        $this->dispatcher->dispatch($getKeyEvent, EncryptKeyEvents::LOAD_KEY);

        // If the event is returned with a key, then override the parameter defined key.
        if (null !== $getKeyEvent->getKey()) {
            $this->secretKey = $getKeyEvent->getKey();
        }

        // If the key is still empty, then throw an exception.
        if (empty($this->secretKey)) {
            throw new EncryptException('The bundle specshaper\encrypt-bundle requires a parameter.yml value for "encrypt_key"
            Use cli command "php bin/console encrypt:genkey" to create a key, or set via a listener on the EncryptKeyEvents::LOAD_KEY event');
        }

        // Decode the key
        $key = base64_decode($this->secretKey);

        $keyLengthOctet = mb_strlen($key, '8bit');

        if (32 !== $keyLengthOctet) {
            throw new \Exception("Needs a 256-bit key, '".($keyLengthOctet * 8)."'bit given!");
        }

        return $key;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    private function getSaltedKey(): ?string
    {
        return hash_pbkdf2("sha256", $this->getSecretKey(), $this->getSalt(), 1000, 32, true);
    }
}
