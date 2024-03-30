<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\MutationPlugin;

use App\DataType\Price;
use App\DataType\ProductName;
use App\DataType\Stock;
use App\Service\UserService;
use Wedrix\Watchtower\Resolver\Node;

function call_create_product_mutation(
    Node $node
): mixed
{
    /**
     * @var UserService
     */
    $userService = $node->context()['user_service'] ?? throw new \Exception('Invalid context value! \'user_service\' unset.');

    $name = new ProductName($node->args()['name'] ?? throw new \Exception('Invalid args! \'name\' unset.'));
    $stock = new Stock((string) $node->args()['stock'] ?? throw new \Exception('Invalid args! \'stock\' unset.'));
    $price = new Price($node->args()['price'] ?? throw new \Exception('Invalid args! \'price\' unset.'));

    $product = $userService->createProduct(
        name: $name,
        stock: $stock,
        price: $price
    );

    return [
        'id' => $product->id(),
        'name' => $product->name(),
        'stock' => $product->stock(),
        'price' => $product->price()
    ];
}