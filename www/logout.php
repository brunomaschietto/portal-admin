<?php
require_once __DIR__ . '/includes/auth.php';
use App\Repository\UserRepository;
use App\Service\UserService;
use App\Controller\UserController;

$userRepo = new UserRepository($pdo);
$userService = new UserService($userRepo);
$userController = new UserController($userService);

$userController->logout();
header('Location: /login.php');
exit;
