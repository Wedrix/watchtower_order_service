<?php

declare(strict_types=1);

namespace App\DataType;

use GraphQL\Error\UserError;

readonly class Role
{
    private string $value;

    public function __construct(
        string $value
    )
    {
        $value = \trim($value);

        if (empty($value)) {
            throw new UserError('Invalid Role! The value cannot be empty.');
        }

        if (!\in_array($value, ['ROLE_ADMIN','ROLE_USER'])) {
            throw new UserError('Invalid Role! Must be either \'ROLE_ADMIN\' or \'ROLE_USER\'.');
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}