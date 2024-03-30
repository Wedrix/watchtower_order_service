<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\AuthorizorPlugin;

use App\Repository\OrderRepository;
use App\Service\AuthService;
use GraphQL\Error\UserError;
use Wedrix\Watchtower\Resolver\Node;
use Wedrix\Watchtower\Resolver\Result;

function authorize_product_lines_result(
    Result $result,
    Node $node
): void
{
    /**
     * @var AuthService
     */
    $auth = $node->context()['auth_service'] ?? throw new \Exception('Invalid context value! Unset \'auth_service\'.');

    /**
     * @var OrderRepository
     */
    $orderRepository = $node->context()['order_repository'] ?? throw new \Exception('Invalid context value! Unset \'order_repository\'.');

    if (!$auth->hasSession()) {
        throw new UserError('Unauthorized! Sign in to view product lines.');
    }

    $authUser = $auth->session()->user();

    if ($authUser->role() === 'ROLE_USER') {
        if ($node->unwrappedParentType() === 'Order') {
            /**
             * @var array<string,mixed>
             */
            $parentOrderResultOutput = $node->root();

            $ownerOfOrderOfProductLine = $orderRepository->orderWithId($parentOrderResultOutput['id'])->user();

            if ($authUser->id() === $ownerOfOrderOfProductLine->id()) {
                return;
            }
        }
        
        throw new UserError('Unauthorized! Only Admins can view other users\' product lines.');
    }

    if ($authUser->role() === 'ROLE_ADMIN') {
        return;
    }

    throw new \Exception('Invalid user role!');
}