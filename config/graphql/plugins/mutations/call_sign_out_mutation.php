<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\MutationPlugin;

use Wedrix\Watchtower\Resolver\Node;

use App\Service\UserService;

function call_sign_out_mutation(
    Node $node
): mixed
{
    /**
     * @var UserService
     */
    $userService  = $node->context()['user_service'] ?? throw new \Exception('Invalid context value! \'user_service\' unset.');

    return $userService->signOut();
}