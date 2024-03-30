<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\ScalarTypeDefinition\DateTimeTypeDefinition;

use GraphQL\Error\UserError;
use GraphQL\Language\AST\StringValueNode;

/**
 * Serializes an internal value to include in a response.
 */
function serialize(
    \DateTimeImmutable $value
): string
{
    return $value->format(\DateTimeImmutable::ATOM);
}

/**
 * Parses an externally provided value (query variable) to use as an input
 */
function parseValue(
    string $value
): \DateTimeImmutable
{
    return \date_create_immutable($value) 
        ?: throw new UserError("Invalid DateTime! '$value' cannot be parsed as a valid DateTime value.");
}

/**
 * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
 * 
 * E.g. 
 * {
 *   user(createdAt: "2021-01-24T05:16:41+00:00") 
 * }
 *
 * @param array<string,mixed>|null $variables
 */
function parseLiteral(
    $value, 
    ?array $variables = null
): \DateTimeImmutable
{
    if (!$value instanceof StringValueNode) {
        throw new UserError('Invalid DateTime! The value must be passed as a String.');
    }

    return parseValue($value->value);
}