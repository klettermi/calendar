<?php
// app/controllers/AuthController.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        // 회원가입 처리
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $email = trim($_POST['email']);
        
        if (User::findByUsername($username)) {
            $error = "이미 존재하는 사용자명입니다.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $newUserId = User::register($username, $hashedPassword, $email);
            if ($newUserId) {
                $_SESSION['user'] = [
                    'id' => $newUserId,
                    'username' => $username,
                    'role' => 'user'
                ];
                header("Location: /dashboard.php");
                exit;
            } else {
                $error = "회원가입에 실패했습니다.";
            }
        }
    } else {
        // 로그인 처리
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $user = User::authenticate($username, $password);
        if ($user) {
            $_SESSION['user'] = [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role
            ];
            header("Location: /dashboard.php");
            exit;
        } else {
            $error = "존재하지 않는 사용자이거나, 아이디/비밀번호가 올바르지 않습니다.";
        }
    }
}
?>