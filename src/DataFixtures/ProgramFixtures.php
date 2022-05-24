<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $program = new Program();
        $program->setTitle('Walking dead');
        $program->setSynopsis('Des zombies envahissent la terre');
        $program->setCategorie($this->getReference('category_Action'));
        $manager->persist($program);
        

        $program = new Program();
        $program->setTitle('Breaking Bad');
        $program->setSynopsis('Walter White envahit la terre');
        $program->setCategorie($this->getReference('category_Action'));
        $manager->persist($program);
        

        $program = new Program();
        $program->setTitle('The Office');
        $program->setSynopsis('Michael Scoot envahit la terre');
        $program->setCategorie($this->getReference('category_Comedie'));
        $manager->persist($program);
        

        $program = new Program();
        $program->setTitle('Rome');
        $program->setSynopsis('Les Romains envahissent la terre');
        $program->setCategorie($this->getReference('category_Historique'));
        $manager->persist($program);

        $program = new Program();
        $program->setTitle('Les Sopranos');
        $program->setSynopsis('La Mafia envahit la terre');
        $program->setCategorie($this->getReference('category_Action'));
        $manager->persist($program);

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
