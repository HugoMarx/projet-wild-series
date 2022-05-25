<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for ($i = 1; $i <= 40; $i++) {

            $season = new Season();
            $season->setProgram($this->getReference('program_'. rand(1, 10)));
            $season->setNumber(rand(1, 12));
            $season->setYear(rand(1990, 2022));
            $season->setDescription('Description ' . $i);
            $manager->persist($season);

            $this->addReference('season_'. $i, $season);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            CategoryFixtures::class,
            ProgramFixtures::class
        ];
    }
}
