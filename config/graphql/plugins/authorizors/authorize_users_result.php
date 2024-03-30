<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\AuthorizorPlugin;

use App\Service\AuthService;
use GraphQL\Error\UserError;
use Wedrix\Watchtower\Resolver\Node;
use Wedrix\Watchtower\Resolver\Result;

function authorize_users_result(
    Result $result,
    Node $node
): void
{
    /**
     * @var AuthService
     */
    $auth = $node->context()['auth_service'] ?? throw new \Exception('Invalid context value! Unset \'auth_service\'.');

    if (!$auth->hasSession()) {
        throw new UserError('Unauthorized! Sign in to view users.');
    }

    $authUser = $auth->session()->user();

    if ($authUser->role() === 'ROLE_USER') {
        throw new UserError('Unauthorized! Only Admins can view other users.');
    }

    if ($authUser->role() === 'ROLE_ADMIN') {
        return;
    }

    throw new \Exception('Invalid user role!');
}