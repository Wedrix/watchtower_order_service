<?php

declare(strict_types=1);

namespace App\Repository;

use App\DataType\EmailAddress;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ){}

    public function hasUserWithId(
        int $id
    ): bool
    {
        return $this->entityManager
                    ->createQuery("SELECT COUNT(_user) FROM " . User::class . " _user WHERE _user.id = :id")
                    ->setParameter('id', $id)
                    ->getSingleScalarResult()
                    > 0;
    }

    public function hasUserWithEmail(
        EmailAddress $email
    ): bool
    {
        return $this->entityManager
                    ->createQuery("SELECT COUNT(_user) FROM " . User::class . " _user WHERE _user.email = :email")
                    ->setParameter('email', (string) $email)
                    ->getSingleScalarResult()
                    > 0;
    }

    public function userWithId(
        int $id
    ): User
    {
        return $this->entityManager
                    ->createQuery("SELECT _user FROM " . User::class . " _user WHERE _user.id = :id")
                    ->setParameter('id', $id)
                    ->getOneOrNullResult()
                    ?? throw new \Exception("No user with id '$id' found.");
    }

    public function userWithEmail(
        EmailAddress $email
    ): User
    {
        return $this->entityManager
                    ->createQuery("SELECT _user FROM " . User::class . " _user WHERE _user.email = :email")
                    ->setParameter('email', (string) $email)
                    ->getOneOrNullResult()
                    ?? throw new \Exception("No user with email '$email' found.");
    }
}