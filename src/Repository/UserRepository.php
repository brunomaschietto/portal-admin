<?php
namespace App\Repository;

use PDO;
use App\Entity\User;

class UserRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function save(User $user): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (username, password_hash, full_name, created_at) 
             VALUES (:username, :password_hash, :full_name, :created_at)"
        );

        $stmt->execute([
            ':username'      => $user->getUsername(),
            ':password_hash' => $user->getPasswordHash(),
            ':full_name'     => $user->getFullName(),
            ':created_at'    => $user->getCreatedAt()
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $user->setId($id);
        return $id;
    }

    public function findById(int $id): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $user = new User($row['username'], $row['password_hash'], $row['full_name']);
        $user->setId((int) $row['id']);
        return $user;
    }

    public function findByUsername(string $username): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $user = new User($row['username'], $row['password_hash'], $row['full_name']);
        $user->setId((int) $row['id']);
        return $user;
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
