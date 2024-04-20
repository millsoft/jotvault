<?php

namespace App\Tests\Service;

use SpecShaper\EncryptBundle\Encryptors\EncryptorFactory;
use SpecShaper\EncryptBundle\Encryptors\EncryptorInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EncryptTest extends KernelTestCase
{

    private EncryptorInterface $encryptor;

    protected function setUp(): void
    {

        self::bootKernel();

        $container = static::getContainer();

        /**
         * @var EncryptorFactory $encryptorFactory
         */
        $encryptorFactory = $container->get(EncryptorFactory::class);

        //get the env variable SPEC_SHAPER_ENCRYPT_KEY:
        $encryptKey = $_ENV['SPEC_SHAPER_ENCRYPT_KEY'];

        /**
         * @var EncryptorInterface $encryptor
         */
        $this->encryptor = $encryptorFactory->createService($encryptKey);
    }

    public function testEncryption(): void
    {
        $encrypted = $this->encryptor->encrypt("test");
        $decrypted = $this->encryptor->decrypt($encrypted);
        $this->assertEquals("test", $decrypted);
    }

    public function testEncryptionWithSalt(): void
    {
        $this->encryptor->setSalt("salt");
        $encrypted = $this->encryptor->encrypt("test");
        $decrypted = $this->encryptor->decrypt($encrypted);
        $this->assertEquals("test", $decrypted);
    }

    public function testEncryptionWithInvalidKey(): void
    {
        $this->encryptor->setSecretKey("encrypt");
        $this->expectException(\Exception::class);
        $this->encryptor->encrypt("test");
    }


}
