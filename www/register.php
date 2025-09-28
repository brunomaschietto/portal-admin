<?php
session_start();
require '/var/www/html/vendor/autoload.php';
require __DIR__ . '/includes/db.php';

use App\Repository\UserRepository;
use App\Service\UserService;
use App\Controller\UserController;

$userRepo = new UserRepository($pdo);
$userService = new UserService($userRepo);
$userController = new UserController($userService);

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $fullName = trim($_POST['full_name'] ?? '');

        if (empty($username) || empty($password) || empty($fullName)) {
            throw new Exception('Todos os campos obrigatórios devem ser preenchidos.');
        }

        if (strlen($password) < 6) {
            throw new Exception('A senha deve ter pelo menos 6 caracteres.');
        }

        if ($password !== $confirmPassword) {
            throw new Exception('As senhas não coincidem.');
        }

        $userData = [
            'username' => $username,
            'password' => $password,
            'full_name' => $fullName
        ];

        $userController->register($userData);
        $success = "Usuário registrado com sucesso! Você pode fazer login agora.";
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro - Sistema</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 450px; 
            margin: 0 auto; 
            padding: 20px; 
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container { 
            background: white; 
            padding: 40px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .register-header p {
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
        .btn-register { 
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
        .btn-register:hover { 
            background: #005a87; 
        }
        .btn-register:active {
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
        .success { 
            color: #155724; 
            margin: 0 0 20px 0; 
            padding: 12px; 
            background: #d4edda; 
            border-radius: 6px; 
            border: 1px solid #c3e6cb;
            font-size: 14px;
            text-align: center;
        }
        .login-section {
            margin-top: 25px;
            text-align: center;
            padding: 20px 0;
        }
        .login-section p {
            color: #666;
            margin: 0 0 15px 0;
            font-size: 14px;
        }
        .btn-login-link {
            background: #6c757d;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .btn-login-link:hover {
            background: #5a6268;
            text-decoration: none;
        }
        .form-footer {
            margin-top: 20px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .form-footer p {
            color: #666;
            font-size: 12px;
            margin: 0;
        }
        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            .register-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Registrar</h1>
            <p>Crie sua conta</p>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="full_name">Nome Completo *</label>
                <input type="text" id="full_name" name="full_name" required 
                       value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                       autocomplete="name">
            </div>

            <div class="form-group">
                <label for="username">Usuário *</label>
                <input type="text" id="username" name="username" required 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">Senha *</label>
                <input type="password" id="password" name="password" required 
                       autocomplete="new-password">
                <div class="password-requirements">
                    Mínimo 6 caracteres
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar Senha *</label>
                <input type="password" id="confirm_password" name="confirm_password" required 
                       autocomplete="new-password">
            </div>

            <button type="submit" class="btn-register">Registrar</button>
        </form>

        <div class="login-section">
            <p>Já tem uma conta?</p>
            <a href="/login.php" class="btn-login-link">Fazer Login</a>
        </div>

        <div class="form-footer">
            <p>&copy; 2025 Sistema de Gerenciamento</p>
        </div>
    </div>
</body>
</html>