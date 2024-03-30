<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\FilterPlugin;

use Wedrix\Watchtower\Resolver\Node;
use Wedrix\Watchtower\Resolver\QueryBuilder;

function apply_products_is_low_on_stock_filter(
    QueryBuilder $queryBuilder,
    Node $node
): void
{
    $entityAlias = $queryBuilder->rootAlias();

    $isLowOnStock = $node->args()['queryParams']['filters']['isLowOnStock'];

    if ($isLowOnStock) {
        $queryBuilder->andWhere("$entityAlias.stock < 5");
    }
    else {
        $queryBuilder->andWhere("$entityAlias.stock >= 5");
    }
}