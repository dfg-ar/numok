<?php

namespace Numok\Database;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;
    private static array $config = [];

    private function __construct() {}
    private function __clone() {}

    public static function setConfig(array $config): void {
        self::$config = $config;
    }

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    "pgsql:host=%s;dbname=%s",
                    self::$config['host'],
                    self::$config['database']
                );

                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ];

                self::$instance = new PDO(
                    $dsn,
                    self::$config['username'],
                    self::$config['password'],
                    $options
                );
            } catch (PDOException $e) {
                // Log the error securely without exposing credentials
                error_log("Database connection failed: " . $e->getMessage());
                throw new PDOException("Database connection failed. Please check your configuration.");
            }
        }

        return self::$instance;
    }

    public static function query(string $sql, array $params = []): \PDOStatement {
        try {
            $stmt = self::getInstance()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage() . " SQL: " . $sql);
            throw new PDOException("Database query failed.");
        }
    }

    public static function insert(string $table, array $data, string $primaryKey = 'id'): int {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );

        self::query($sql, array_values($data));

        // PostgreSQL uses sequences named: tablename_columnname_seq
        $sequenceName = sprintf('%s_%s_seq', $table, $primaryKey);
        return (int) self::getInstance()->lastInsertId($sequenceName);
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): int {
        $fields = array_map(fn($field) => "$field = ?", array_keys($data));
        
        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $table,
            implode(', ', $fields),
            $where
        );

        $stmt = self::query($sql, [...array_values($data), ...$whereParams]);
        return $stmt->rowCount();
    }

    public static function delete(string $table, string $where, array $whereParams = []): int {
        $sql = sprintf("DELETE FROM %s WHERE %s", $table, $where);
        $stmt = self::query($sql, $whereParams);
        return $stmt->rowCount();
    }

    public static function transaction(callable $callback): mixed {
        $pdo = self::getInstance();
        
        try {
            $pdo->beginTransaction();
            $result = $callback($pdo);
            $pdo->commit();
            return $result;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}