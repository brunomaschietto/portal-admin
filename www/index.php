<?php
require_once __DIR__ . '/includes/auth.php';
if (isAuthenticated()) {
    header('Location: /clients/list.php');
} else {
    header('Location: /login.php');
}
exit;
