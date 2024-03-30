<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\MutationPlugin;

use App\Service\UserService;
use Wedrix\Watchtower\Resolver\Node;

function call_delete_product_mutation(
    Node $node
): mixed
{
    /**
     * @var UserService
     */
    $userService = $node->context()['user_service'] ?? throw new \Exception('Invalid context value! \'user_service\' unset.');

    $productId = (int) $node->args()['productId'] ?? throw new \Exception('Invalid args! \'productId\' unset.');

    $userService->deleteProduct(
        productId: $productId
    );

    return true;
}