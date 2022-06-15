<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
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
           $manager->persist($admin);

           $contributor = new User;
           $hashedPassword = $this->passwordHasher->hashPassword($contributor, 'password');
           $contributor->setPassword($hashedPassword);
           $contributor->setEmail('user@gmail.com');
           $contributor->setRoles(['ROLE_CONTRIBUTOR']);
           $manager->persist($contributor);
   


        $manager->flush();
    }
}
