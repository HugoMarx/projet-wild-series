<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{

    private UserPasswordHasherInterface $passwordHasher;
    
public function __construct(UserPasswordHasherInterface $passwordHasher)
{

    $this->passwordHasher = $passwordHasher;
    
}
    public function load(ObjectManager $manager): void
    {
    
           // ... e.g. get the user data from a registration form
           $admin = new User();
           // hash the password (based on the security.yaml config for the $admin class)
           $hashedPassword = $this->passwordHasher->hashPassword(
               $admin,
               'password'
           );
           $admin->setPassword($hashedPassword);
           $admin->setEmail('marx.hugo@gmail.com');
           $admin->setRoles(['ROLE_ADMIN']);
           $admin->addOwnedProgram($this->getReference('program_1'));
           $manager->persist($admin);

           $contributor = new User;
           $hashedPassword = $this->passwordHasher->hashPassword($contributor, 'password');
           $contributor->setPassword($hashedPassword);
           $contributor->setEmail('user@gmail.com');
           $contributor->setRoles(['ROLE_CONTRIBUTOR']);
           $contributor->addOwnedProgram($this->getReference('program_2'));
           $manager->persist($contributor);
   


        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            ProgramFixtures::class,

        ];
    }
}
