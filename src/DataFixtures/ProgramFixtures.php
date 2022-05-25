<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 1; $i <= 10 ; $i++) {
            $program = new Program();
            $program->setTitle('Program_' . $i);
            $program->setSynopsis('Description_' . $i);
            $program->setCategorie($this->getReference('category_' . CategoryFixtures::CATEGORIES[rand(0, 8)]));
            $this->addReference('program_'.$i, $program);
            $manager->persist($program);
        }

        $manager->flush();
    }

   /* public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
          
        ];
    }*/
}
