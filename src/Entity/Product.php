<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;

#[Entity]
class Product
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    private int $id;

    /**
     * @var Collection<int,ProductLine>
     */
    #[OneToMany(targetEntity:ProductLine::class,mappedBy:'product',cascade:['persist','remove'])]
    private Collection $productLines;

    public function __construct(
        #[Column]
        private string $name,
        #[Column]
        private int $stock,
        #[Column(type:'decimal',precision:12,scale:2)]
        private string $price
    )
    {
        $this->productLines = new ArrayCollection();
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setName(
        string $name
    ): void
    {
        $this->name = $name;
    }

    public function stock(): int
    {
        return $this->stock;
    }

    public function setStock(
        int $stock
    ): void
    {
        $this->stock = $stock;
    }

    public function price(): string
    {
        return $this->price;
    }

    public function setPrice(
        string $price
    ): void
    {
        $this->price = $price;
    }

    /**
     * @return Collection<int,ProductLine>
     */
    public function productLines(): Collection
    {
        return $this->productLines;
    }
}