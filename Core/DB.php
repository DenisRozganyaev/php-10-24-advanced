<?php

namespace Core;

use PDO;

class DB
{
    static protected PDO|null $pdo = null;

    static public function connect(): PDO
    {
        if (is_null(static::$pdo)) {
            $dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME');

            static::$pdo = new PDO(
                $dsn,
                getenv('DB_USER'),
                getenv('DB_PASSWORD'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }

        return static::$pdo;
    }
}
