<?php
// app/controllers/ScheduleController.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Schedule.php';

if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    if ($action == 'create') {
        $allowed_types = ['일반', '교육', '세미나', '회식'];
        $type = trim($_POST['type']);
        $type = preg_replace('/[\x00-\x1F\x7F\x{200B}]/u', '', $type); 
        if (class_exists('Normalizer')) {
            $type = Normalizer::normalize($type, Normalizer::FORM_C);
        } else {
            // intl 확장이 설치되어 있지 않다면, 설치하거나 확인하세요.
            die("Normalizer 클래스가 없습니다. PHP intl 확장을 활성화하세요.");
        }
        if (!in_array($type, $allowed_types, true)) {
            die("유효하지 않은 일정 종류입니다. 전달된 값: " . htmlspecialchars($type));
        }

        $data = [
            'user_id'      => $user_id,
            'type'         => $type,
            'title'        => $_POST['title'],
            'location'     => $_POST['location'],
            'start_time'   => $_POST['start_time'],
            'end_time'     => $_POST['end_time'],
            'participants' => implode(',', $_POST['participants'] ?? [])
        ];
        Schedule::create($data);
        header("Location: /dashboard.php");
        exit;
    } elseif ($action == 'update') {
        $id = $_POST['id'];
        $data = [
            'user_id'      => $user_id,
            'type'         => $_POST['type'],
            'title'        => $_POST['title'],
            'location'     => $_POST['location'],
            'start_time'   => $_POST['start_time'],
            'end_time'     => $_POST['end_time'],
            'participants' => implode(',', $_POST['participants'] ?? [])
        ];
        Schedule::update($id, $data);
        header("Location: /dashboard.php");
        exit;
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        Schedule::delete($id, $user_id);
        header("Location: /dashboard.php");
        exit;
    } elseif ($action == 'duplicate') {
        $id = $_POST['id'];
        Schedule::duplicate($id, $user_id);
        header("Location: /dashboard.php");
        exit;
    }
}
?>