<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\MutationPlugin;

use App\Service\UserService;
use Wedrix\Watchtower\Resolver\Node;

function call_update_order_mutation(
    Node $node
): mixed
{
    /**
     * @var UserService
     */
    $userService = $node->context()['user_service'] ?? throw new \Exception('Invalid context value! \'user_service\' unset.');

    $orderId = (int) $node->args()['orderId'] ?? throw new \Exception('Invalid args! \'orderId\' unset.');
    $userId = isset($node->args()['userId']) ? (int) $node->args()['userId'] : null;
    $productLines = isset($node->args()['productLines']) ? $node->args()['productLines'] : null;

    $order = $userService->updateOrder(
        orderId: $orderId,
        userId: $userId,
        productLines: $productLines
    );

    return [
        'id' => $order->id()
    ];
}