<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: User::class)]
class UserPassword
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher){}

    public function prePersist(User $user): void
    {
        $this->updatePassword($user);
    }

    public function preUpdate(User $user): void
    {
        $this->updatePassword($user);
    }

    private function updatePassword(User $user): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
    }
}