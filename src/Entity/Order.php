<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name:"`order`")]
class Order
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    private int $id;

    /**
     * @param Collection<int,ProductLine> $productLines
     */
    public function __construct(
        #[ManyToOne(targetEntity:User::class,inversedBy:'orders')]
        private User $user,
        #[OneToMany(targetEntity:ProductLine::class,mappedBy:'order',cascade:['persist','remove'])]
        private Collection $productLines
    ){}

    public function id(): int
    {
        return $this->id;
    }

    /**
     * @return Collection<int,ProductLine>
     */
    public function productLines(): Collection
    {
        return $this->productLines;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function setUser(
        User $user
    ): void
    {
        $this->user = $user;
    }
}