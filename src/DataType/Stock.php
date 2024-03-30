<?php

declare(strict_types=1);

namespace App\DataType;

use GraphQL\Error\UserError;

readonly class Stock
{
    private string $value;

    public function __construct(
        string $value
    )
    {
        $value = \trim($value);

        if (empty($value)) {
            throw new UserError('Invalid Stock! The value cannot be empty.');
        }

        if (!\ctype_digit($value)) {
            throw new UserError('Invalid Stock! All characters must digits.');
        }

        if ((((int) $value) > 1000000) || (((int) $value) < 0) ) {
            throw new UserError('Invalid Stock! Must be in the range 1000000 > stock >= 0.');
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}