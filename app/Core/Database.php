<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    public static function getConnection(): PDO
    {
        $config = require __DIR__ . '/../../config/Database.php';

        try {
            return new PDO(
                "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}",
                $config['username'],
                $config['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }
}
