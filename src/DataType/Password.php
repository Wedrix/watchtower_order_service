<?php

declare(strict_types=1);

namespace App\DataType;

use GraphQL\Error\UserError;

readonly class Password
{
    private string $value;

    public function __construct(
        string $value
    )
    {
        $value = \trim($value);

        if (empty($value)) {
            throw new UserError('Invalid Password! The value cannot be empty.');
        }

        if (\strlen($value) < 8) {
            throw new UserError('Invalid Password! Must be at least 8 characters long.');
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}