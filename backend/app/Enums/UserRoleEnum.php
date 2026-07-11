<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    /**
     * Get all possible values for validation
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all possible values as string for validation rules
     */
    public static function valuesString(): string
    {
        return implode(',', self::values());
    }
}
