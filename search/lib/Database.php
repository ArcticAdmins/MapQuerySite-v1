<?php
namespace lib;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?Database $instance = null;
    private string $db_file;
    private ?PDO $conn;

    private function __construct($configPath)
    {
        $config = require $configPath;
        $this->db_file = $config['database']['url'];
        $this->connect();
    }

    public static function getInstance($configPath): ?Database
    {
        if (self::$instance === null) {
            self::$instance = new self($configPath);
        }
        return self::$instance;
    }

    private function connect(): void
    {
        if (!file_exists($this->db_file)) {
            touch($this->db_file);
        }

        try {
            $this->conn = new PDO("sqlite:" . $this->db_file);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 创建 users 表
            $this->conn->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY,
                    username TEXT NOT NULL UNIQUE,
                    password TEXT NOT NULL
                )
            ");
        } catch (PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            $this->conn = null;
        }
    }

    public function getConnection(): ?PDO
    {
        return $this->conn;
    }

    public function query($sql, $params = []): ?PDOStatement
    {
        if ($this->conn === null) {
            return null;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
    }
}