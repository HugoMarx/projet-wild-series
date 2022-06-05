<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\Service\Slugify;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{

    private Slugify $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $episodeCount = 0;

        for ($i = 1; $i <= 5000; $i++) {

            $episode = new Episode();
            $episode->setSeason($this->getReference('season_'. $faker->numberBetween(1, 500)));
            $episode->setTitle($faker->words(rand(2, 6), true));
            $episode->setNumber($episodeCount += 1);
            $episode->setSynopsis($faker->paragraph(3));
            $episode->setSlug($this->slugify->generate($episode->getTitle()));
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

