<?php
// app/models/User.php
class User {
    public $id;
    public $username;
    public $password; // 해시값 저장
    public $role;     // 'user' 또는 'admin'

    // 로그인 인증: 사용자명과 비밀번호 검증
    public static function authenticate($username, $password) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $u = new User();
            $u->id = $user['id'];
            $u->username = $user['username'];
            $u->role = $user['role'];
            return $u;
        }
        return false;
    }
    
    // 사용자명으로 조회 (중복 체크용)
    public static function findByUsername($username) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    // 회원가입: 새 사용자 등록
    public static function register($username, $password, $email) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')");
        if ($stmt->execute([$username, $password, $email])) {
            return $pdo->lastInsertId();
        }
        return false;
    }
    
    // 전체 사용자 조회 (참가자 선택용)
    public static function getAllUsers() {
        global $pdo;
        $stmt = $pdo->query("SELECT id, username FROM users");
        return $stmt->fetchAll();
    }
}
