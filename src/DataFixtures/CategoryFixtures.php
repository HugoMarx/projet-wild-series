<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{

    public const CATEGORIES = ['Horreur', 'Comedie', 'Drame', 'Historique', 'Action', 'Aventure', 'Documentaire', 'Romance','Science-fiction'];

    public function load(ObjectManager $manager)
    {
        foreach (self::CATEGORIES as $value){
            $category = new Categorie();
            $category->setName($value);
            $manager->persist($category);
        }
        
        $manager->flush();
    }
}