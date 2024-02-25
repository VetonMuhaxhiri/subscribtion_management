<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i = 1; $i <= 10; $i++) {
            $subscription = new Subscription();
            $subscription->setName('Subscription ' . $i);
            $subscription->setDescription('Subscription ' . $i);
            $subscription->setPrice($i);
            $subscription->setDuration($i);
            $manager->persist($subscription);
        }

        $manager->flush();
    }
}
