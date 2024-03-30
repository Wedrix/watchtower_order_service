<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\MutationPlugin;

use App\Service\UserService;
use Wedrix\Watchtower\Resolver\Node;

function call_delete_order_mutation(
    Node $node
): mixed
{
    /**
     * @var UserService
     */
    $userService = $node->context()['user_service'] ?? throw new \Exception('Invalid context value! \'user_service\' unset.');

    $orderId = (int) $node->args()['orderId'] ?? throw new \Exception('Invalid args! \'orderId\' unset.');

    $userService->deleteOrder(
        orderId: $orderId
    );

    return true;
}