<?php
namespace Simit\Core;

use InvalidArgumentException;

final class Sql
{
    public static function identifier(string $value, array $allowed): string
    {
        if (!in_array($value, $allowed, true) || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $value)) {
            throw new InvalidArgumentException('SQL identifier is not allowed.');
        }
        return $value;
    }

    public static function identifiers(array $values, array $allowed): array
    {
        return array_map(fn(string $value): string => self::identifier($value, $allowed), $values);
    }

    public static function direction(string $value, string $default = 'DESC'): string
    {
        $value = strtoupper($value);
        return in_array($value, ['ASC', 'DESC'], true) ? $value : $default;
    }
}
