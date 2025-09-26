<?php
namespace App\Entity;

class Client {
    private ?int $id = null;
    private string $name;
    private string $birthDate;
    private string $cpf;
    private ?string $rg;
    private ?string $phone;
    private array $addresses = [];

    public function __construct(string $name, string $birthDate, string $cpf, ?string $rg = null, ?string $phone = null) {
        $this->name = $name;
        $this->birthDate = $birthDate;
        $this->cpf = $cpf;
        $this->rg = $rg;
        $this->phone = $phone;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }

    public function getCpf(): string { return $this->cpf; }
    public function setCpf(string $cpf): void { $this->cpf = $cpf; }

    public function getBirthDate(): string { return $this->birthDate; }
    public function setBirthDate(string $birthDate): void { $this->birthDate = $birthDate; }

    public function getRg(): ?string { return $this->rg; }
    public function setRg(?string $rg): void { $this->rg = $rg; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): void { $this->phone = $phone; }

    public function addAddress(string $address): void {
        $this->addresses[] = $address;
    }

    public function getAddresses(): array {
        return $this->addresses;
    }
}
