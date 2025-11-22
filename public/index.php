<?php

require __DIR__ . '/../vendor/autoload.php';

use Khanev\UrlShortener\Database;

$db = new Database();
echo "✅ Класс Database загружен и подключён к БД!";

// Проверим запрос
$pdo = $db->getConnection();
$stmt = $pdo->query("SELECT DATABASE()");
$dbName = $stmt->fetchColumn();

echo "\n\nТекущая БД: " . $dbName;
