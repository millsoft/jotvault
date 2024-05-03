<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $user = new \App\Entity\User();
            $user->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($user);

            // Reference to use later in Note fixture:
            $this->addReference('user-'.$i, $user);
        }

        $manager->flush();
    }
}
