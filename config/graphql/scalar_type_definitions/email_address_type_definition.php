<?php

declare(strict_types=1);

namespace Wedrix\Watchtower\ScalarTypeDefinition\EmailAddressTypeDefinition;

use App\DataType\EmailAddress;
use GraphQL\Error\UserError;
use GraphQL\Language\AST\StringValueNode;

/**
 * Serializes an internal value to include in a response.
 */
function serialize(
    string $value
): string
{
    return $value;
}

/**
 * Parses an externally provided value (query variable) to use as an input
 */
function parseValue(
    string $value
): string
{
    return (string) new EmailAddress($value);
}

/**
 * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
 * 
 * E.g. "james@123example.com"
 * 
 * @param array<string,mixed>|null $variables
 */
function parseLiteral(
    $value, 
    ?array $variables = null
): string
{
    if (!$value instanceof StringValueNode) {
        throw new UserError('Invalid EmailAddress! The value must be passed as a String.');
    }
    
    return parseValue($value->value);
}