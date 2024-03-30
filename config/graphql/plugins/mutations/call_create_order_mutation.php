<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\MutationPlugin;

use App\Service\UserService;
use Wedrix\Watchtower\Resolver\Node;

function call_create_order_mutation(
    Node $node
): mixed
{
    /**
     * @var UserService
     */
    $userService = $node->context()['user_service'] ?? throw new \Exception('Invalid context value! \'user_service\' unset.');

    $userId = (int) $node->args()['userId'] ?? throw new \Exception('Invalid args! \'userId\' unset.');
    $productLines = $node->args()['productLines'] ?? throw new \Exception('Invalid args! \'productLines\' unset.');

    $order = $userService->createOrder(
        userId: $userId,
        productLines: $productLines
    );

    return [
        'id' => $order->id()
    ];
}