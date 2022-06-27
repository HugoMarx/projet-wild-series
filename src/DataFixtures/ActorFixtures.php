<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class ActorFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('en_EN');

        // $product = new Product();
        // $manager->persist($product);

        for ($i = 0; $i < 10; $i++) {
            $actor = new Actor;
            $actor->setFirstname($faker->firstName());
            $actor->setLastname($faker->lastName());
            $actor->setBirthDate($faker->date());
            $actor->setCreatedAt(new \DateTimeImmutable('now'));

            $actor->addProgram($this->getReference('program_' . rand(1, 100)));
            $actor->addProgram($this->getReference('program_' . rand(1, 100)));
            $actor->addProgram($this->getReference('program_' . rand(1, 100)));



            $manager->persist($actor);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            ProgramFixtures::class,
            CategoryFixtures::class,

        ];
    }
}
