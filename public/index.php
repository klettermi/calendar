<?php
// public/index.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION['user'])) {
    header("Location: /dashboard.php");
    exit;
} else {
    header("Location: /login.php");
    exit;
}
