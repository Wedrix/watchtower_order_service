<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\MutationPlugin;

use App\DataType\EmailAddress;
use App\DataType\Password;
use App\Service\UserService;
use Wedrix\Watchtower\Resolver\Node;

function call_sign_in_mutation(
    Node $node
): mixed
{
    /**
     * @var UserService
     */
    $userService = $node->context()['user_service'] ?? throw new \Exception('Invalid context value! \'user_service\' unset.');

    $email = new EmailAddress($node->args()['email'] ?? throw new \Exception('Invalid args! \'email\' unset.'));
    $password = new Password($node->args()['password'] ?? throw new \Exception('Invalid args! \'password\' unset.'));

    $user = $userService->signIn(
        email: $email,
        password: $password
    );

    return [
        'id' => $user->id(),
        'name' => $user->name(),
        'email' => $user->email(),
        'role' => $user->role()
    ];
}