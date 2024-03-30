<?php

declare(strict_types=1);

namespace App\DataType;

use GraphQL\Error\UserError;

readonly class ProductName
{
    private string $value;

    public function __construct(
        string $value
    )
    {
        $value = \trim($value);

        if (empty($value)) {
            throw new UserError('Invalid ProductName! The value cannot be empty.');
        }

        if (!\ctype_alnum(\str_replace(' ','',$value))) {
            throw new UserError('Invalid ProductName! All characters must either be alphabets, spaces, or digits.');
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}