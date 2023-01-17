<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
    ){}

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setUsername('Admin');
        $admin->setLastname('Doe');
        $admin->setFirstname('John');
        $admin->setEmail('admin@blog.test');
        $admin->setPassword(
            $this->userPasswordHasher->hashPassword($admin, 'Azerty123')
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $superAdmin = new User();
        $superAdmin->setUsername('SuperAdmin');
        $superAdmin->setLastname('Doe');
        $superAdmin->setFirstname('Jane');
        $superAdmin->setEmail('superadmin@blog.test');
        $superAdmin->setPassword(
            $this->userPasswordHasher->hashPassword($superAdmin, 'Azerty123')
        );
        $superAdmin->setRoles(['ROLE_SUPERADMIN']);

        $manager->persist($superAdmin);

        $manager->flush();
    }
}
