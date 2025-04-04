<?php
// app/models/Schedule.php
class Schedule {
    public $id;
    public $user_id;
    public $type;
    public $title;
    public $location;
    public $start_time;
    public $end_time;
    public $participants;
    
    // 일정 등록
    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO schedules (user_id, type, title, location, start_time, end_time, participants) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        return $stmt->execute([
            $data['user_id'],
            $data['type'],
            $data['title'],
            $data['location'],
            $data['start_time'],
            $data['end_time'],
            $data['participants']
        ]);
    }
    
    // 일정 수정 (해당 일정의 등록자만 수정 가능)
    public static function update($id, $data) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE schedules SET type = ?, title = ?, location = ?, start_time = ?, end_time = ?, participants = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([
            $data['type'],
            $data['title'],
            $data['location'],
            $data['start_time'],
            $data['end_time'],
            $data['participants'],
            $id,
            $data['user_id']
        ]);
    }
    
    // 일정 삭제 (해당 일정의 등록자만 삭제 가능)
    public static function delete($id, $user_id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $user_id]);
    }
    
    // 사용자별 일정 조회 (일반 사용자는 자신 일정만, 관리자는 모든 일정)
    public static function getByUser($user_id, $username) {
        global $pdo;
        // user_id 컬럼이 생성자와 일치하거나, participants 문자열에 사용자의 이름이 포함된 일정 조회
        $stmt = $pdo->prepare("SELECT * FROM schedules WHERE user_id = ? OR participants LIKE ?");
        $stmt->execute([$user_id, '%' . $username . '%']);
        return $stmt->fetchAll();
    }
    
    

    public static function getAll() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM schedules");
        return $stmt->fetchAll();
    }

    public static function getById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // 일정 복사: 기존 일정을 그대로 복제 (본인 소유인 경우)
    public static function duplicate($id, $user_id) {
        global $pdo;
        $schedule = self::getById($id);
        if ($schedule) {
            if ($schedule['user_id'] != $user_id) {
                return false;
            }
            unset($schedule['id']); // ID는 자동 생성
            $schedule['user_id'] = $user_id;
            return self::create($schedule);
        }
        return false;
    }
}
