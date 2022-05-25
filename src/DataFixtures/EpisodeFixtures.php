<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 1; $i <= 200; $i++) {

            $episode = new Episode();
            $episode->setSeason($this->getReference('season_'. rand(1, 10)));
            $episode->setTitle('Title_'. $i);
            $episode->setNumber($i);
            $episode->setSynopsis('Description ' . $i);
            $manager->persist($episode);
        }

        $manager->flush();
    }

   public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            SeasonFixtures::class,
               
        ];
    }
}

