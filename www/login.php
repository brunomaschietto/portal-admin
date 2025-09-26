<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/../includes/db.php';

use App\Repository\UserRepository;
use App\Service\UserService;
use App\Controller\UserController;

$userRepo = new UserRepository($pdo);
$userService = new UserService($userRepo);
$userController = new UserController($userService);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userController->login($_POST);
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login</title></head>
<body>
  <h2>Login</h2>
  <?php if ($error): ?><p style="color:red"><?=htmlspecialchars($error)?></p><?php endif; ?>
  <form method="post">
    <label>Usuário: <input name="username" required></label><br>
    <label>Senha: <input type="password" name="password" required></label><br>
    <button type="submit">Entrar</button>
  </form>
</body>
</html>
