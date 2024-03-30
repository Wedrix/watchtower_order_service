<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\MutationPlugin;

use App\Service\UserService;
use Wedrix\Watchtower\Resolver\Node;

function call_delete_user_mutation(
    Node $node
): mixed
{
    /**
     * @var UserService
     */
    $userService = $node->context()['user_service'] ?? throw new \Exception('Invalid context value! \'user_service\' unset.');

    $userId = (int) $node->args()['userId'] ?? throw new \Exception('Invalid args! \'userId\' unset.');

    $userService->deleteUser(
        userId: $userId
    );

    return true;
}