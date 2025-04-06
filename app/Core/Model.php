<?php

namespace App\Core;

use App\Core\Database;
use PDO;

abstract class Model
{
    protected static string $table;

    public static function all(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM " . static::$table);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM " . static::$table . " WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function where(string $column, string $operator, mixed $value): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM " . static::$table . " WHERE $column $operator :value");
        $stmt->execute(['value' => $value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save(): bool
    {
        $pdo = Database::getConnection();
        $fields = get_object_vars($this);
        unset($fields['table']);

        if (isset($this->id)) {
            // Обновление записи
            $setClause = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($fields)));
            $stmt = $pdo->prepare("UPDATE " . static::$table . " SET $setClause WHERE id = :id");
            $fields['id'] = $this->id;
        } else {
            // Создание записи
            $columns = implode(", ", array_keys($fields));
            $placeholders = implode(", ", array_map(fn($key) => ":$key", array_keys($fields)));
            $stmt = $pdo->prepare("INSERT INTO " . static::$table . " ($columns) VALUES ($placeholders)");
        }

        return $stmt->execute($fields);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM " . static::$table . " WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}