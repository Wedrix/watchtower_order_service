<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\MutationPlugin;

use App\DataType\Price;
use App\DataType\ProductName;
use App\DataType\Stock;
use App\Service\UserService;
use Wedrix\Watchtower\Resolver\Node;

function call_update_product_mutation(
    Node $node
): mixed
{
    /**
     * @var UserService
     */
    $userService = $node->context()['user_service'] ?? throw new \Exception('Invalid context value! \'user_service\' unset.');

    $productId = (int) $node->args()['productId'] ?? throw new \Exception('Invalid args! \'productId\' unset.');
    $name = isset($node->args()['name']) ? new ProductName($node->args()['name']) : null;
    $stock = isset($node->args()['stock']) ? new Stock($node->args()['stock']) : null;
    $price = isset($node->args()['price']) ? new Price($node->args()['price']) : null;

    $product = $userService->updateProduct(
        productId: $productId,
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