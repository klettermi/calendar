<?php
// app/views/dashboard.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Schedule.php';

$user_id = $_SESSION['user']['id'];
$user_role = $_SESSION['user']['role'];
$username = $_SESSION['user']['username'];

if ($user_role == 'admin') {
    $schedules = Schedule::getAll();
} else {
    $schedules = Schedule::getByUser($user_id, $username);
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>대시보드 - 캘린더 프로젝트</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; }
        .dashboard-container { margin-top: 40px; }
        .card { margin-bottom: 20px; }
    </style>
</head>
<body>
    <!-- 네비게이션 바 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container">
         <a class="navbar-brand" href="#">캘린더 프로젝트</a>
         <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="메뉴 토글">
             <span class="navbar-toggler-icon"></span>
         </button>
         <div class="collapse navbar-collapse" id="navbarContent">
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
    
    <!-- 메인 콘텐츠 영역 -->
    <div class="container dashboard-container">
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h2>내 일정</h2>
         <a href="/schedule_form.php" class="btn btn-success">새 일정 추가</a>
      </div>
      <div class="card">
         <div class="card-body">
            <?php if(count($schedules) > 0): ?>
            <div class="table-responsive">
              <table class="table table-striped table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>ID</th>
                      <th>종류</th>
                      <th>제목</th>
                      <th>장소</th>
                      <th>시작 시간</th>
                      <th>종료 시간</th>
                      <th>참여자</th>
                      <th>작업</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($schedules as $schedule): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($schedule['id']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['type']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['title']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['location']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['end_time']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['participants']); ?></td>
                        <td>
                          <div class="btn-group" role="group">
                              <form action="/schedule_form.php" method="get">
                                  <input type="hidden" name="id" value="<?php echo $schedule['id']; ?>">
                                  <button type="submit" class="btn btn-sm btn-primary">수정</button>
                              </form>
                              <form action="/scheduleController.php" method="post">
                                  <input type="hidden" name="id" value="<?php echo $schedule['id']; ?>">
                                  <input type="hidden" name="action" value="delete">
                                  <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('정말 삭제하시겠습니까?')">삭제</button>
                              </form>
                              <form action="/scheduleController.php" method="post">
                                  <input type="hidden" name="id" value="<?php echo $schedule['id']; ?>">
                                  <input type="hidden" name="action" value="duplicate">
                                  <button type="submit" class="btn btn-sm btn-secondary">복제</button>
                              </form>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
              </table>
            </div>
            <?php else: ?>
              <p class="text-center">일정이 없습니다.</p>
            <?php endif; ?>
         </div>
      </div>
      <!-- 캘린더 보기 버튼 -->
      <div class="text-center mt-3">
            <a href="/calendar.php" class="btn btn-outline-primary">캘린더 보기</a>
      </div>
    </div>
    
    <!-- Bootstrap JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>