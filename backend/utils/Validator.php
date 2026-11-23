<?php
declare(strict_types=1);

class Validator
{
    public static function fields(array $data, array $allowed): void
    {
        $unknown = array_diff(array_keys($data), $allowed);
        if ($unknown) { throw new ValidationException('Unknown or read-only fields: ' . implode(', ', $unknown) . '.'); }
    }

    public static function text(mixed $value, string $field, int $max = 100, bool $nullable = false): ?string
    {
        if ($nullable && ($value === null || $value === '')) { return null; }
        if (!is_string($value) || trim($value) === '' || mb_strlen(trim($value)) > $max) {
            throw new ValidationException("$field must be non-empty text of at most $max characters.");
        }
        return trim($value);
    }

    public static function integer(mixed $value, string $field, int $min = 1, int $max = 2147483647): int
    {
        if ((!is_int($value) && !is_string($value)) || filter_var($value, FILTER_VALIDATE_INT) === false || $value < $min || $value > $max) {
            throw new ValidationException("$field must be a whole number between $min and $max.");
        }
        return (int) $value;
    }

    public static function money(mixed $value, string $field): string
    {
        if ((!is_string($value) && !is_int($value) && !is_float($value)) || !preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D', (string) $value)) {
            throw new ValidationException("$field must be a nonnegative amount with at most two decimal places (maximum 99999999.99).");
        }
        [$whole, $fraction] = array_pad(explode('.', (string) $value), 2, '');
        return (string) (int) $whole . '.' . str_pad($fraction, 2, '0');
    }

    public static function choice(mixed $value, string $field, array $allowed): string
    {
        if (!in_array($value, $allowed, true)) { throw new ValidationException("$field must be one of: " . implode(', ', $allowed) . '.'); }
        return $value;
    }

    public static function date(mixed $value, string $field): string
    {
        $date = is_string($value) ? DateTimeImmutable::createFromFormat('!Y-m-d', $value) : false;
        if (!$date || $date->format('Y-m-d') !== $value || $value < '1000-01-01' || $value > '9999-12-31') {
            throw new ValidationException("$field must be a valid YYYY-MM-DD date.");
        }
        return $value;
    }

    public static function vin(mixed $value): string
    {
        $vin = strtoupper(self::text($value, 'vin', 17));
        if (!preg_match('/^[A-HJ-NPR-Z0-9]{17}$/D', $vin)) { throw new ValidationException('VIN must contain 17 letters or digits, excluding I, O and Q.'); }
        return $vin;
    }
}
