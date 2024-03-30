<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
class ProductLine
{
    public function __construct(
        #[Id]
        #[ManyToOne(targetEntity:Product::class,inversedBy:'productLines')]
        private Product $product,
        #[Id]
        #[ManyToOne(targetEntity:Order::class,inversedBy:'productLines')]
        private Order $order,
        #[Column]
        private int $quantity
    ){}

    public function product(): Product
    {
        return $this->product;
    }

    public function order(): Order
    {
        return $this->order;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}