<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\Plugin\SelectorPlugin;

use Wedrix\Watchtower\Resolver\Node;
use Wedrix\Watchtower\Resolver\QueryBuilder;

function apply_product_is_low_on_stock_selector(
    QueryBuilder $queryBuilder,
    Node $node
): void
{
    $entityAlias = $queryBuilder->rootAlias();

    $queryBuilder->addSelect("
        CASE 
            WHEN $entityAlias.stock < 5 
            THEN TRUE
            ELSE FALSE
        END 
        AS isLowOnStock"
    );  
}