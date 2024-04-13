<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Note extends Fixture
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
            $manager->persist($note);
        }

        $manager->flush();
    }
}
