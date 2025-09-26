<?php
namespace App\Entity;

class Address {
    private ?int $id = null;
    private int $clientId;
    private ?string $street;
    private ?string $number;
    private ?string $complement;
    private ?string $neighborhood;
    private ?string $city;
    private ?string $state;
    private ?string $zip;
    private string $createdAt;

    public function __construct(
        int $clientId,
        ?string $street = null,
        ?string $number = null,
        ?string $complement = null,
        ?string $neighborhood = null,
        ?string $city = null,
        ?string $state = null,
        ?string $zip = null
    ) {
        $this->clientId = $clientId;
        $this->street = $street;
        $this->number = $number;
        $this->complement = $complement;
        $this->neighborhood = $neighborhood;
        $this->city = $city;
        $this->state = $state;
        $this->zip = $zip;
        $this->createdAt = date('Y-m-d H:i:s');
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getClientId(): int { return $this->clientId; }
    public function setClientId(int $clientId): void { $this->clientId = $clientId; }

    public function getStreet(): ?string { return $this->street; }
    public function setStreet(?string $street): void { $this->street = $street; }

    public function getNumber(): ?string { return $this->number; }
    public function setNumber(?string $number): void { $this->number = $number; }

    public function getComplement(): ?string { return $this->complement; }
    public function setComplement(?string $complement): void { $this->complement = $complement; }

    public function getNeighborhood(): ?string { return $this->neighborhood; }
    public function setNeighborhood(?string $neighborhood): void { $this->neighborhood = $neighborhood; }

    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $city): void { $this->city = $city; }

    public function getState(): ?string { return $this->state; }
    public function setState(?string $state): void { $this->state = $state; }

    public function getZip(): ?string { return $this->zip; }
    public function setZip(?string $zip): void { $this->zip = $zip; }

    public function getCreatedAt(): string { return $this->createdAt; }
    public function setCreatedAt(string $createdAt): void { $this->createdAt = $createdAt; }
}
