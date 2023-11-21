<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        // user@user.fr
        for ($i = 0; $i < 10; $i++) {
            $object = (new Post())
                ->setContent($faker->paragraph(3))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setOwner($this->getReference('user'))
            ;
            $manager->persist($object);
        }

        // random user
        $users = $manager->getRepository(User::class)->findAll();
        for ($i = 0; $i < 10; $i++) {
            $object = (new Post())
                ->setContent($faker->paragraph(3))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setOwner($users[array_rand($users)])
            ;
            $manager->persist($object);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
