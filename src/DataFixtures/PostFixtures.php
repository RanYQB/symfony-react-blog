<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\String\Slugger\SluggerInterface;


class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private SluggerInterface $slugger,
        private UserRepository $userRepository,
    ){

    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 1; $i <= 25; $i++){
            $blogPost = new BlogPost();
            $blogPost->setTitle($faker->sentence());
            $blogPost->setContent($faker->text());
            $users = $this->userRepository->findAll();
            $author = array_rand($users);
            $blogPost->setAuthor($users[$author]);
            $blogPost->setSlug($this->slugger->slug($blogPost->getTitle())->lower());
            $blogPost->setPublishedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-30 days', '-7 days' )));
            $manager->persist($blogPost);
        }

        $manager->flush();
    }


    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}
