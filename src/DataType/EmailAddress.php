<?php

declare(strict_types=1);

namespace App\DataType;

use GraphQL\Error\UserError;

readonly class EmailAddress
{
    private string $value;

    public function __construct(
        string $value
    )
    {
        $value = \trim($value);

        if (empty($value)) {
            throw new UserError('Invalid EmailAddress! The value cannot be empty.');
        }

        if (\filter_var($value, \FILTER_VALIDATE_EMAIL) === false) {
            throw new UserError('Invalid EmailAddress!');
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}