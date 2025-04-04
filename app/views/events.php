<?php
// public/events.php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(["error" => "권한이 없습니다."]);
    exit;
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/models/Schedule.php';

$user_id = $_SESSION['user']['id'];

// 관리자라면 모든 이벤트, 일반 사용자는 본인 일정만 반환
if ($_SESSION['user']['role'] === 'admin') {
    $sql = "SELECT id, type, title, location, start_time, end_time FROM schedules";
    $stmt = $pdo->query($sql);
} else {
    $sql = "SELECT id, type, title, location, start_time, end_time FROM schedules WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
}

$events = [];
while ($row = $stmt->fetch()) {
    $events[] = [
        'id'    => $row['id'],
        'title' => $row['title'] . " (" . $row['type'] . ")",
        'start' => $row['start_time'],
        'end'   => $row['end_time'],
        // 추가 옵션: URL, 색상 등
    ];
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($events);
