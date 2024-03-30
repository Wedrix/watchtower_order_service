<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\ScalarTypeDefinition\LimitTypeDefinition;

use GraphQL\Error\UserError;
use GraphQL\Language\AST\IntValueNode;

/**
 * Serializes an internal value to include in a response.
 */
function serialize(
    int $value
): int
{
    return $value;
}

/**
 * Parses an externally provided value (query variable) to use as an input
 */
function parseValue(
    int $value
): int
{
    if (($value < 1) || ($value > 100)) {
        throw new UserError('Invalid Limit! Must be in the range 1 <= value <= 100.');
    }

    return $value;
}

/**
 * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
 * 
 * E.g. 
 * {
 *   limit: 1,
 * }
 *
 * @param array<string,mixed>|null $variables
 */
function parseLiteral(
    $value, 
    ?array $variables = null
): int
{
    if (!$value instanceof IntValueNode) {
        throw new UserError('Invalid Limit! The value must be passed as an Int.');
    }

    return parseValue((int) $value->value);
}