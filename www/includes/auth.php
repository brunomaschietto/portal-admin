<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id(true);
}

function loginUser($userId, $username) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    session_regenerate_id(true);
}

function isAuthenticated() {
    return !empty($_SESSION['user_id']);
}

function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: /login.php');
        exit;
    }
}

function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        setcookie(session_name(), '', time() - 42000);
    }
    session_destroy();
}
