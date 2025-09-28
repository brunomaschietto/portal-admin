<?php
session_start();
require '/var/www/vendor/autoload.php';
require __DIR__ . '/includes/db.php';

use App\Repository\UserRepository;
use App\Service\UserService;
use App\Controller\UserController;

$userRepo = new UserRepository($pdo);
$userService = new UserService($userRepo);
$userController = new UserController($userService);

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = $userController->login($_POST);
}

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Sistema</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 400px; 
            margin: 0 auto; 
            padding: 20px; 
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container { 
            background: white; 
            padding: 40px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .login-header p {
            color: #666;
            margin: 8px 0 0 0;
            font-size: 14px;
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: bold; 
            color: #333;
            font-size: 14px;
        }
        input { 
            width: 100%; 
            padding: 14px; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            box-sizing: border-box; 
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input:focus { 
            border-color: #007cba; 
            outline: none; 
            box-shadow: 0 0 0 2px rgba(0,124,186,0.1); 
        }
        .btn-login { 
            background: #007cba; 
            color: white; 
            padding: 14px 20px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: bold;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        .btn-login:hover { 
            background: #005a87; 
        }
        .btn-login:active {
            transform: translateY(1px);
        }
        .error { 
            color: #dc3545; 
            margin: 0 0 20px 0; 
            padding: 12px; 
            background: #f8d7da; 
            border-radius: 6px; 
            border: 1px solid #f5c6cb;
            font-size: 14px;
            text-align: center;
        }
        .form-footer {
            margin-top: 30px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .form-footer p {
            color: #666;
            font-size: 12px;
            margin: 0;
        }
        .register-section {
            margin-top: 25px;
            text-align: center;
            padding: 20px 0;
        }
        .register-section p {
            color: #666;
            margin: 0 0 15px 0;
            font-size: 14px;
        }
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            .login-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Login</h1>
            <p>Acesse sua conta</p>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="username">Usuário</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn-login">Entrar</button>
        </form>
        <div class="register-section">
            <p>Não tem uma conta?</p>
            <a href="/register.php" class="btn-register">Registrar-se</a>
        </div>

        <div class="form-footer">
            <p>&copy; 2025 Sistema de Gerenciamento</p>
        </div>
    </div>
</body>
</html>