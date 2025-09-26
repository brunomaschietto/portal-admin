<?php
namespace App\Controller;

use App\Service\UserService;
use InvalidArgumentException;

class UserController {
    private UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function register(array $data): void {
        try {
            $this->userService->createUser($data);
            header("Location: /login.php?registered=1");
            exit;
        } catch (InvalidArgumentException $e) {
            echo "Validation error: " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Unexpected error: " . $e->getMessage();
        }
    }

    public function login(array $data): void {
        try {
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            $user = $this->userService->authenticate($username, $password);
            if ($user) {
                session_start();
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getUsername();
                header("Location: /clients/list.php");
                exit;
            } else {
                echo "Invalid username or password.";
            }
        } catch (\Exception $e) {
            echo "Unexpected error: " . $e->getMessage();
        }
    }

    public function logout(): void {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /login.php?logged_out=1");
        exit;
    }
}
