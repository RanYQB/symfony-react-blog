<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;


class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
    ){

    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 1; $i <= 20; $i++){
            $user = new User();
            $user->setUsername($faker->userName());
            $user->setLastname($faker->lastName());
            $user->setFirstname($faker->firstName());
            $user->setEmail($faker->email());
            if($i % 3 === 0){
                $user->setRoles(["ROLE_WRITER"]);
            } elseif ($i % 4 === 0 && $i !== 12 ){
                $user->setRoles(["ROLE_EDITOR"]);
            } else {
                $user->setRoles(["ROLE_COMMENTATOR"]);
            }
            $user->setPassword(
                $this->userPasswordHasher->hashPassword($user, 'Azerty123'.$i)
            );
            $manager->persist($user);
        }

        $manager->flush();
    }

}
