<?php
// app/views/schedule_form.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Schedule.php';
require_once __DIR__ . '/../models/User.php';

// 편집/신규 등록 구분
$is_edit = false;
$schedule = [
    'id' => '',
    'type' => '',
    'title' => '',
    'location' => '',
    'start_time' => '',
    'end_time' => '',
    'participants' => ''
];

if (isset($_GET['id'])) {
    $is_edit = true;
    $scheduleData = Schedule::getById($_GET['id']);
    if ($scheduleData) {
        $schedule = $scheduleData;
    }
} else {
    // 신규 등록 모드: GET 파라미터로 'date'가 전달되면 해당 날짜, 없으면 오늘 날짜
    if (isset($_GET['date'])) {
        $dateStr = $_GET['date'];
    } else {
        $dateStr = date('Y-m-d'); // 오늘 날짜
    }
    // 기본 시작/종료 시간 설정
    $schedule['start_time'] = $dateStr . " 09:00:00";
    $schedule['end_time']   = $dateStr . " 10:00:00";
}

// 일정 종류와 사용자 목록
$types = ['일반', '교육', '세미나', '회식'];
$users = User::getAllUsers();

// participants 문자열을 배열로 변환 (체크 상태 확인용)
$selectedParticipants = explode(',', $schedule['participants']);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title><?php echo $is_edit ? "수정" : "등록"; ?> 일정 - 캘린더 프로젝트</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS (CDN 또는 로컬) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .schedule-form-container {
            margin-top: 50px;
        }
        .schedule-form-card {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .checkbox-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            margin-right: 10px;
        }
        .checkbox-item input[type="checkbox"] {
            margin-right: 5px;
        }
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

<div class="container schedule-form-container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="schedule-form-card">
                <h2 class="text-center mb-4"><?php echo $is_edit ? "일정 수정" : "일정 등록"; ?></h2>
                <form action="/scheduleController.php" method="post">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($schedule['id']); ?>">
                        <input type="hidden" name="action" value="update">
                    <?php else: ?>
                        <input type="hidden" name="action" value="create">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="type" class="form-label">종류</label>
                        <select name="type" id="type" class="form-select" required>
                            <?php foreach ($types as $option): ?>
                                <option value="<?php echo $option; ?>" <?php if ($schedule['type'] === $option) echo "selected"; ?>>
                                    <?php echo $option; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">제목</label>
                        <input type="text" name="title" id="title" class="form-control" 
                               value="<?php echo htmlspecialchars($schedule['title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">장소</label>
                        <input type="text" name="location" id="location" class="form-control" 
                               value="<?php echo htmlspecialchars($schedule['location']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="start_time" class="form-label">시작 시간</label>
                        <input type="datetime-local" name="start_time" id="start_time" class="form-control" 
                               value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($schedule['start_time']))); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_time" class="form-label">종료 시간</label>
                        <input type="datetime-local" name="end_time" id="end_time" class="form-control" 
                               value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($schedule['end_time']))); ?>" required>
                    </div>
                    
                    <!-- 체크박스 형태의 참여자 목록 -->
                    <div class="mb-3">
                        <label class="form-label">참여자</label>
                        <div class="checkbox-container">
                            <?php foreach ($users as $user): ?>
                                <?php
                                $usernameValue = $user['username'];
                                $isChecked = in_array($usernameValue, $selectedParticipants);
                                ?>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="participants[]" value="<?php echo $usernameValue; ?>"
                                        <?php if ($isChecked) echo "checked"; ?>>
                                    <?php echo htmlspecialchars($usernameValue); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary"><?php echo $is_edit ? "수정" : "등록"; ?> 일정</button>
                    </div>
                </form>
                <div class="mt-3 text-center">
                    <a href="/dashboard.php" class="btn btn-secondary">대시보드로 돌아가기</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
