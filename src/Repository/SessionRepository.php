<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Session;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SessionRepository
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ){}

    public function hasSessionOfUserWithId(
        User $user,
        string $id
    ): bool
    {
        return $this->entityManager
                    ->createQuery(
                        "SELECT COUNT(_session) FROM " . Session::class . " _session
                        JOIN _session.user _user 
                        WHERE _session.id = :id
                        AND _user = :user"
                    )
                    ->setParameter('id', $id)
                    ->setParameter('user', $user)
                    ->getSingleScalarResult()
                    > 0;
    }

    public function sessionOfUserWithId(
        User $user,
        string $id
    ): Session
    {
        return $this->entityManager
                    ->createQuery(
                        "SELECT _session FROM " . Session::class . " _session
                        JOIN _session.user _user 
                        WHERE _session.id = :id
                        AND _user = :user"
                    )
                    ->setParameter('id', $id)
                    ->setParameter('user', $user)
                    ->getOneOrNullResult()
                    ?? throw new \Exception("No session of user '{$user->id()}' with id '$id' found.");
    }
}