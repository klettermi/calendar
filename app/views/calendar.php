<?php
// public/calendar.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

// 현재 로그인 사용자 이름
$username = $_SESSION['user']['username'] ?? '';

// 현재 연/월 설정 (쿼리스트링으로 전달된 값 사용, 없으면 현재 연/월)
$year  = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');

// 달의 첫날, 총 날짜, 시작 요일 계산
$firstDayTimestamp = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = date('t', $firstDayTimestamp);
$startDayOfWeek = date('w', $firstDayTimestamp); // 0: 일요일, 6: 토요일

// 이전/다음 달 계산
$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}
$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

// 이벤트 조회 (생성자이거나, 참여자로 포함된 일정)
$user_id = $_SESSION['user']['id'];
$user_role = $_SESSION['user']['role'];
global $pdo;

if ($user_role === 'admin') {
    $sql = "SELECT id, title, start_time, end_time FROM schedules WHERE YEAR(start_time) = ? AND MONTH(start_time) = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$year, $month]);
} else {
    $sql = "SELECT id, title, start_time, end_time FROM schedules 
            WHERE YEAR(start_time) = ? AND MONTH(start_time) = ? 
              AND (user_id = ? OR participants LIKE ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$year, $month, $user_id, '%' . $username . '%']);
}
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 날짜별 이벤트 배열 (키: 해당 날짜(1~31))
$eventMap = [];
foreach ($events as $event) {
    $day = date('j', strtotime($event['start_time']));
    $eventMap[$day][] = $event;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title><?php echo $year; ?>년 <?php echo $month; ?>월 달력 - 캘린더 프로젝트</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .nav {
            text-align: center;
            margin: 20px 0;
        }
        .nav a {
            margin: 0 10px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .calendar {
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            width: 100%;
            max-width: 900px;
            table-layout: fixed; /* 고정 레이아웃 */
        }
        /* 요일 헤더 스타일 */
        .calendar thead th {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            height: 40px;          /* 헤더 높이를 줄임 */
            padding: 4px;          /* 패딩 조정 */
            font-size: 0.9rem;     /* 글자 크기 조정 */
        }
        .calendar td {
            border: 1px solid #dee2e6;
            vertical-align: top;
            height: 120px; /* 날짜 셀 높이 */
            padding: 8px;
            overflow: hidden; /* 내용이 넘칠 경우 숨김 */
            text-align: left;
        }
        .day-number {
            font-weight: bold;
            margin-bottom: 4px;
        }
        .day-link {
            color: #000;
            text-decoration: none;
        }
        .day-link:hover {
            text-decoration: underline;
        }
        .event {
            background: #ffc107;
            border-radius: 4px;
            margin: 2px 0;
            padding: 2px 4px;
            font-size: 0.9rem;
        }
        .event a {
            color: inherit;
            text-decoration: none;
        }
        .add-link {
            display: block;
            margin-top: 6px;
            font-size: 0.9rem;
            color: #007bff;
            text-decoration: none;
        }
        .add-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="nav">
        <a href="?year=<?php echo $prevYear; ?>&month=<?php echo $prevMonth; ?>">&laquo; 이전</a>
        <?php echo "{$year}년 {$month}월"; ?>
        <a href="?year=<?php echo $nextYear; ?>&month=<?php echo $nextMonth; ?>">다음 &raquo;</a>
    </div>
    <table class="calendar">
        <thead>
            <tr>
                <th>일</th>
                <th>월</th>
                <th>화</th>
                <th>수</th>
                <th>목</th>
                <th>금</th>
                <th>토</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $currentDay = 1;
            $startDayOfWeek = date('w', $firstDayTimestamp);
            $totalCells = ceil(($daysInMonth + $startDayOfWeek) / 7) * 7;
            
            for ($cell = 0; $cell < $totalCells; $cell++) {
                if ($cell % 7 == 0) {
                    echo "<tr>";
                }
                if ($cell < $startDayOfWeek || $currentDay > $daysInMonth) {
                    echo "<td></td>";
                } else {
                    $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $currentDay);
                    echo "<td>";
                    
                    // 날짜 번호
                    echo "<div class='day-number'>{$currentDay}</div>";
                    
                    // 이벤트 유무 확인
                    if (isset($eventMap[$currentDay])) {
                        // 이벤트가 있는 경우
                        foreach ($eventMap[$currentDay] as $event) {
                            echo "<div class='event'>";
                            echo "<a class='day-link' href='/schedule_detail.php?id=" . $event['id'] . "'>";
                            echo htmlspecialchars($event['title']);
                            echo "<br><small>" . date('H:i', strtotime($event['start_time'])) . " ~ " . date('H:i', strtotime($event['end_time'])) . "</small>";
                            echo "</a>";
                            echo "</div>";
                        }
                        // 새 일정 추가 링크
                        echo "<a class='add-link' href='/schedule_form.php?date={$dateStr}'>+ 새 일정 추가</a>";
                    } else {
                        // 이벤트가 없는 경우: 셀 전체를 일정 추가 링크로
                        echo "<a class='day-link' href='/schedule_form.php?date={$dateStr}' style='display:block; width:100%; height:100%;'>";
                        echo "</a>";
                    }
                    
                    echo "</td>";
                    $currentDay++;
                }
                if ($cell % 7 == 6) {
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
    <div class="nav">
        <a href="/dashboard.php">대시보드로 돌아가기</a>
    </div>
</body>
</html>
