<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\MutationPlugin;

use App\DataType\EmailAddress;
use App\DataType\Name;
use App\DataType\Password;
use App\DataType\Role;
use App\Service\UserService;
use Wedrix\Watchtower\Resolver\Node;

function call_update_user_mutation(
    Node $node
): mixed
{
    /**
     * @var UserService
     */
    $userService = $node->context()['user_service'] ?? throw new \Exception('Invalid context value! \'user_service\' unset.');

    $userId = (int) $node->args()['userId'] ?? throw new \Exception('Invalid args! \'userId\' unset.');
    $name = isset($node->args()['name']) ? new Name($node->args()['name']) : null;
    $email = isset($node->args()['email']) ? new EmailAddress((string) $node->args()['email']) : null;
    $password = isset($node->args()['password']) ? new Password($node->args()['password']) : null;
    $role = isset($node->args()['role']) ? new Role($node->args()['role']) : null;

    $user = $userService->updateUser(
        userId: $userId,
        name: $name,
        email: $email,
        password: $password,
        role: $role
    );

    return [
        'id' => $user->id(),
        'name' => $user->name(),
        'email' => $user->email(),
        'role' => $user->role()
    ];
}