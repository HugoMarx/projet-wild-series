<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Service\Slugify;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{

    private Slugify $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('en_EN');

        for ($i = 1; $i <= 100; $i++) {
            $program = new Program();
            $program->setTitle($faker->name());
            $program->setSynopsis($faker->realText($maxNbChars = 200, $indexSize = 2));
            $program->setPoster($faker->imageUrl(360, 360, 'Movie', true, '', true, 'jpg'));
            $program->setYear($faker->year());
            $program->setCountry($faker->countryCode());
            $program->setCategorie($this->getReference('category_' . CategoryFixtures::CATEGORIES[rand(0, 8)]));
            $program->setSlug($this->slugify->generate($program->getTitle()));
            $program->setUpdatedAt(new \DateTime('now'));
            $this->addReference('program_' . $i, $program);
            $manager->persist($program);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            CategoryFixtures::class,
        ];
    }
}
