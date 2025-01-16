<?php

// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Vérifiez si l'utilisateur admin existe déjà
        $repository = $manager->getRepository(User::class);
        if ($repository->findOneBy(['email' => 'admin@example.com'])) {
            return;
        }

        // Créez un utilisateur admin
        $admin = new User();
        $admin->setLastname('Admin');
        $admin->setFirstname('Super');
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);

        // Hachez le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);

        $manager->persist($admin);
        $manager->flush();
    }
}

