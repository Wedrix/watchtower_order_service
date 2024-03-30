<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\AuthorizorPlugin;

use App\Service\AuthService;
use GraphQL\Error\UserError;
use Wedrix\Watchtower\Resolver\Node;
use Wedrix\Watchtower\Resolver\Result;

function authorize_orders_result(
    Result $result,
    Node $node
): void
{
    /**
     * @var AuthService
     */
    $auth = $node->context()['auth_service'] ?? throw new \Exception('Invalid context value! Unset \'auth_service\'.');

    if (!$auth->hasSession()) {
        throw new UserError('Unauthorized! Sign in to view orders.');
    }

    $authUser = $auth->session()->user();

    if ($authUser->role() === 'ROLE_USER') {
        if ($node->unwrappedParentType() === 'User') {
            /**
             * @var array<string,mixed>
             */
            $parentUserResultOutput = $node->root();

            if ($authUser->id() === $parentUserResultOutput['id']) {
                return;
            }
        }
        
        throw new UserError('Unauthorized! Only Admins can view other users\' orders.');
    }

    if ($authUser->role() === 'ROLE_ADMIN') {
        return;
    }

    throw new \Exception('Invalid user role!');
}