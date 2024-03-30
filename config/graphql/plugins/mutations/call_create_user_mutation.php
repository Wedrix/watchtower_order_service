<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\MutationPlugin;

use App\DataType\EmailAddress;
use App\DataType\Name;
use App\DataType\Password;
use App\DataType\Role;
use App\Service\UserService;
use Wedrix\Watchtower\Resolver\Node;

function call_create_user_mutation(
    Node $node
): mixed
{
    /**
     * @var UserService
     */
    $userService = $node->context()['user_service'] ?? throw new \Exception('Invalid context value! \'user_service\' unset.');

    $name = new Name($node->args()['name'] ?? throw new \Exception('Invalid args! \'name\' unset.'));
    $email = new EmailAddress((string) $node->args()['email'] ?? throw new \Exception('Invalid args! \'email\' unset.'));
    $password = new Password($node->args()['password'] ?? throw new \Exception('Invalid args! \'password\' unset.'));
    $role = new Role($node->args()['role'] ?? throw new \Exception('Invalid args! \'role\' unset.'));

    $user = $userService->createUser(
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