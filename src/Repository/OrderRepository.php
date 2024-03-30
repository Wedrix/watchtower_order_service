<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class OrderRepository
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ){}

    public function hasOrderWithId(
        int $id
    ): bool
    {
        return $this->entityManager
                    ->createQuery("SELECT COUNT(_order) FROM " . Order::class . " _order WHERE _order.id = :id")
                    ->setParameter('id', $id)
                    ->getSingleScalarResult()
                    > 0;
    }

    public function orderWithId(
        int $id
    ): Order
    {
        return $this->entityManager
                    ->createQuery("SELECT _order FROM " . Order::class . " _order WHERE _order.id = :id")
                    ->setParameter('id', $id)
                    ->getOneOrNullResult()
                    ?? throw new \Exception("No order with id '$id' found.");
    }
}