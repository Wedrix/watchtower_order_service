<?php

declare(strict_types=1);

namespace App\DataType;

use GraphQL\Error\UserError;

readonly class Name
{
    private string $value;

    public function __construct(
        string $value
    )
    {
        $value = \trim($value);

        if (empty($value)) {
            throw new UserError('Invalid Name! The value cannot be empty.');
        }

        if (!\ctype_alpha(\str_replace(' ','',$value))) {
            throw new UserError('Invalid Name! All characters must either be alphabets or spaces.');
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}