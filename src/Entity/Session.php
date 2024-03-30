<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Uid\Uuid;

#[Entity]
class Session
{
    #[Id]
    #[Column(type:'guid')]
    private string $id;

    private bool $isNew = false;

    public function __construct(
        #[Id]
        #[ManyToOne(targetEntity:User::class,inversedBy:'sessions')]
        private User $user
    )
    {
        $this->id = (string) Uuid::v4();

        $this->isNew = true;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function isNew(): bool
    {
        return $this->isNew;
    }
}