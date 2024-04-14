<?php

namespace SpecShaper\EncryptBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * CLI command to generate a salted 256-bit encryption key.
 */
#[AsCommand(name: 'encrypt:genkey-salted')]
class GenKeyCommandSalted extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Generate a salted key');
        $this->addArgument('salt', InputArgument::REQUIRED, 'The salt to use');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $salt = $input->getArgument('salt');
        $keyLength = 32;

        //TODO: get the base password from env or a secure location
        $password = "LoremIpsumDolorem1979";

        if (empty($salt) || strlen($salt) < 8) {
            throw new \Exception("Salt bae must be at least 8 characters long.");
        }

        $key = hash_pbkdf2("sha256", $password, $salt, 10000, $keyLength, true);

        $io = new SymfonyStyle($input, $output);
        $io->title('Generated Key');
        $io->success('Key is: ' . base64_encode($key));

        return Command::SUCCESS;
    }
}
