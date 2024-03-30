<?php

declare(strict_types=1);

namespace App\DataType;

use GraphQL\Error\UserError;

readonly class Price
{
    private string $value;

    public function __construct(
        string $value
    )
    {
        $value = \trim($value);

        if (empty($value)) {
            throw new UserError('Invalid Price! The value cannot be empty.');
        }

        if (!\preg_match('/^\d+\.\d{2}$/',$value)) {
            throw new UserError('Invalid Price! Must be in the format \'D*.DD\'.');
        }

        if ((((float) $value) > 1000000000) || (((int) $value) < 0) ) {
            throw new UserError('Invalid Price! Must be in the range 1000000000.00 > price >= 0.00.');
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}