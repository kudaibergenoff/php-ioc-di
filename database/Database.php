<?php

namespace Database;

use PDO;

class Database
{
    private PDO $dbh;

    public function __construct()
    {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $dbname = $_ENV['DB_NAME'] ?? 'php_ioc_di';
        $user = $_ENV['DB_USER'] ?? 'user';
        $pass = $_ENV['DB_PASS'] ?? 'Aa123123';
        $port = $_ENV['DB_PORT'] ?? '54328';

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $this->dbh = new PDO($dsn, $user, $pass);
    }

    public function query(string $sql, array $params = [], $class = \stdClass::class): array
    {
        $sth = $this->dbh->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(PDO::FETCH_CLASS, $class);
    }
}
