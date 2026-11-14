<?php
// app/Config/Database.php

class Database
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo === null) {
            $host    = self::env('DB_HOST', 'localhost');
            $db      = self::env('DB_NAME', '');
            $user    = self::env('DB_USER', '');
            $pass    = self::env('DB_PASS', '');
            $charset = self::env('DB_CHARSET', 'utf8mb4');

            $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                error_log('Error de conexión a la base de datos: ' . $e->getMessage());
                throw new RuntimeException('No se pudo conectar a la base de datos.');
            }
        }

        return self::$pdo;
    }

    private static function env(string $key, string $default = ''): string
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        }

        return (string) $value;
    }
}