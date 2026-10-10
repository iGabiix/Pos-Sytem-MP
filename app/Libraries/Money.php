<?php

namespace App\Libraries;

use InvalidArgumentException;

final class Money
{
    public const MAX_CENTS = 9999999999;

    public static function cents(string $value): int
    {
        if (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D', $value)) {
            throw new InvalidArgumentException('Enter a valid price with at most two decimal places.');
        }
        $parts = explode('.', $value);
        return ((int) $parts[0] * 100) + (int) str_pad($parts[1] ?? '', 2, '0');
    }

    public static function decimal(int $cents): string
    {
        return intdiv($cents, 100) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }
}

