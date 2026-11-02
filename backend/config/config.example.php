<?php
declare(strict_types=1);

// Copy to config.php and set your local XAMPP database settings there.
// Environment variables override these defaults. Never commit config.php.
class Config
{
    public static function DB_HOST(): string { return self::env('DB_HOST', '127.0.0.1'); }
    public static function DB_PORT(): string { return self::env('DB_PORT', '3306'); }
    public static function DB_NAME(): string { return self::env('DB_NAME', 'pitstop_os'); }
    public static function DB_USER(): string { return self::env('DB_USER', 'root'); }
    public static function DB_PASSWORD(): string { return self::env('DB_PASSWORD', ''); }

    private static function env(string $name, string $default): string
    {
        $value = getenv($name);
        return $value === false ? $default : $value;
    }
}
