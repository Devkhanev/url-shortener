<?php

namespace Khanev\UrlShortener;
use PDO;
use PDOException;

class Database
{

    private PDO $pdo;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']);

        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }

    }
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}