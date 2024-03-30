<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductRepository
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ){}

    public function hasProductWithId(
        int $id
    ): bool
    {
        return $this->entityManager
                    ->createQuery("SELECT COUNT(product) FROM " . Product::class . " product WHERE product.id = :id")
                    ->setParameter('id', $id)
                    ->getSingleScalarResult()
                    > 0;
    }

    public function productWithId(
        int $id
    ): Product
    {
        return $this->entityManager
                    ->createQuery("SELECT product FROM " . Product::class . " product WHERE product.id = :id")
                    ->setParameter('id', $id)
                    ->getOneOrNullResult()
                    ?? throw new \Exception("No product with id '$id' found.");
    }
}