<?php
// public/schedule_detail.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Schedule.php';

if (!isset($_GET['id'])) {
    die("일정 ID가 제공되지 않았습니다.");
}

$schedule = Schedule::getById($_GET['id']);
if (!$schedule) {
    die("일정을 찾을 수 없습니다.");
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>일정 상세 - 캘린더 프로젝트</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 800px; margin-top: 40px; }
        .table { background: #fff; }
        .btn-group { margin-top: 20px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/dashboard.php">캘린더 프로젝트</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="메뉴 토글">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link">환영합니다, <?php echo htmlspecialchars($_SESSION['user']['username']); ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/logout.php">로그아웃</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="mb-4">일정 상세 정보</h2>
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td><?php echo htmlspecialchars($schedule['id']); ?></td>
        </tr>
        <tr>
            <th>종류</th>
            <td><?php echo htmlspecialchars($schedule['type']); ?></td>
        </tr>
        <tr>
            <th>제목</th>
            <td><?php echo htmlspecialchars($schedule['title']); ?></td>
        </tr>
        <tr>
            <th>장소</th>
            <td><?php echo htmlspecialchars($schedule['location']); ?></td>
        </tr>
        <tr>
            <th>시작 시간</th>
            <td><?php echo htmlspecialchars($schedule['start_time']); ?></td>
        </tr>
        <tr>
            <th>종료 시간</th>
            <td><?php echo htmlspecialchars($schedule['end_time']); ?></td>
        </tr>
        <tr>
            <th>참여자</th>
            <td><?php echo htmlspecialchars($schedule['participants']); ?></td>
        </tr>
    </table>
    <div class="btn-group" role="group">
        <a href="/schedule_form.php?id=<?php echo $schedule['id']; ?>" class="btn btn-primary">수정</a>
        <form action="/scheduleController.php" method="post" onsubmit="return confirm('정말 삭제하시겠습니까?');">
            <input type="hidden" name="id" value="<?php echo $schedule['id']; ?>">
            <input type="hidden" name="action" value="delete">
            <button type="submit" class="btn btn-danger">삭제</button>
        </form>
        <a href="/dashboard.php" class="btn btn-secondary">대시보드로 돌아가기</a>
    </div>
</div>
<!-- Bootstrap JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
