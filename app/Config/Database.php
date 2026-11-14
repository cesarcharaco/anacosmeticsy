<?php
// app/Config/Database.php

class Database {
    private static $host = 'localhost';
    private static $db   = 'yerm0726_anacosmeticsy';
    private static $user = 'yerm0726_anacosmeticsy';
    private static $pass = 'Anacosmeticsy26';
    private static $charset = 'utf8mb4';
    private static $pdo = null;
    //bd:yerm0726_anacosmeticsy
    //user:yerm0726_anacosmeticsy
    //contraeña:Anacosmeticsy26
    public static function connect() {
        if (self::$pdo === null) {
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db . ";charset=" . self::$charset;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$pdo = new PDO($dsn, self::$user, self::$pass, $options);
            } catch (\PDOException $e) {
                // En producción registrar en log, no mostrar en pantalla
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        return self::$pdo;
    }
}