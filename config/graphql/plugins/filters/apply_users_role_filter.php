<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\FilterPlugin;

use Wedrix\Watchtower\Resolver\Node;
use Wedrix\Watchtower\Resolver\QueryBuilder;

function apply_users_role_filter(
    QueryBuilder $queryBuilder,
    Node $node
): void
{
    $entityAlias = $queryBuilder->rootAlias();

    $role = $node->args()['queryParams']['filters']['role'];
    
    $queryBuilder->andWhere("$entityAlias.role = :role")
                ->setParameter('role', $role);
}