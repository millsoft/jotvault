<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NoteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for($i=0;$i<10;$i++){
            $note = new \App\Entity\Note();
            $note->setTitle('Note '.$i);
            $note->setContent('Content '.$i);
            $note->setCreatedAt(new \DateTimeImmutable());
            $user = $this->getReference('user-'.rand(0,4));
            $note->setUser($user);
            $manager->persist($note);
        }

        $manager->flush();
    }


    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
