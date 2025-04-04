<?php
// app/config/db.php
$host = 'db'; // Docker Compose 서비스명
$db   = 'calendar_db';
$user = 'localhost';
$pass = '12345';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => true, // 이미 설정했다면 그대로 둡니다.
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // strict 모드 해제 (테스트용)
    $pdo->exec("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
