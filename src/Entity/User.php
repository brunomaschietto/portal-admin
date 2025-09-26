<?php
namespace App\Entity;

class User {
    private ?int $id = null;
    private string $username;
    private string $passwordHash;
    private string $fullName;
    private string $createdAt;

    public function __construct(string $username, string $passwordHash, string $fullName) {
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->fullName = $fullName;
        $this->createdAt = date('Y-m-d H:i:s');
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): void { $this->username = $username; }

    public function getPasswordHash(): string { return $this->passwordHash; }
    public function setPasswordHash(string $passwordHash): void { $this->passwordHash = $passwordHash; }

    public function getFullName(): string { return $this->fullName; }
    public function setFullName(string $fullName): void { $this->fullName = $fullName; }

    public function getCreatedAt(): string { return $this->createdAt; }
}
