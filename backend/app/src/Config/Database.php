<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            self::connect();
        }
        return self::$connection;
    }

    private static function connect(): void
    {
        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'mysql';
        $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: 3306;
        $database = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'developmentdb';
        $username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'developer';
        $password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: 'secret123';

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        self::$connection = new PDO(
            $dsn,
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    public static function closeConnection(): void
    {
        self::$connection = null;
    }
}
