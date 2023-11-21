<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $pwd = 'test';

        $object = (new User())
            ->setEmail('user@user.fr')
            ->setPassword($pwd)
            ->setLastname($faker->lastName)
        ;
        $manager->persist($object);
        $this->addReference('user', $object);

        $object = (new User())
            ->setEmail('admin@user.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($pwd)
            ->setLastname($faker->lastName)
        ;
        $manager->persist($object);
        $this->addReference('admin', $object);

        for ($i = 0; $i < 10; $i++) {
            $object = (new User())
                ->setEmail($faker->email)
                ->setPassword($pwd)
                ->setLastname($faker->lastName)
            ;
            $manager->persist($object);
        }

        $manager->flush();
    }
}
