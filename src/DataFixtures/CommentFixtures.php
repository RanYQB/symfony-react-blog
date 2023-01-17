<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Repository\BlogPostRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;


class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private BlogPostRepository $blogPostRepository,
    ){

    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 1; $i <= 80; $i++){
            $comment = new Comment();
            $comment->setContent($faker->text());
            $users = $this->userRepository->findUsers('ROLE_COMMENTATOR');
            $author = array_rand($users);
            $blogPosts = $this->blogPostRepository->findAll();
            $blogPost = array_rand($blogPosts);
            $comment->setPost($blogPosts[$blogPost]);
            $comment->setAuthor($users[$author]);
            $comment->setPublishedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 days', 'now' )));
            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            PostFixtures::class
        ];
    }
}
