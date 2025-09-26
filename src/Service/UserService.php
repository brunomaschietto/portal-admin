<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use InvalidArgumentException;

class UserService {
    private UserRepository $repository;

    public function __construct(UserRepository $repository) {
        $this->repository = $repository;
    }

    public function createUser(array $data): int {
        if (empty($data['username']) || empty($data['password']) || empty($data['full_name'])) {
            throw new InvalidArgumentException("Username, password and full name are required.");
        }

        $existing = $this->repository->findByUsername($data['username']);
        if ($existing) {
            throw new InvalidArgumentException("Username already exists.");
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        $user = new User($data['username'], $passwordHash, $data['full_name']);

        return $this->repository->save($user);
    }

    public function getUserById(int $id): ?User {
        return $this->repository->findById($id);
    }

    public function getUserByUsername(string $username): ?User {
        return $this->repository->findByUsername($username);
    }

    public function authenticate(string $username, string $password): ?User {
        $user = $this->repository->findByUsername($username);
        if ($user && password_verify($password, $user->getPasswordHash())) {
            return $user;
        }
        return null;
    }

    public function deleteUser(int $id): bool {
        return $this->repository->delete($id);
    }
}
