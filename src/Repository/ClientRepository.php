<?php
namespace App\Repository;

use PDO;
use App\Entity\Client;

class ClientRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function save(Client $client): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO clients (name, birth_date, cpf, rg, phone) 
             VALUES (:name, :birth_date, :cpf, :rg, :phone)"
        );
        $stmt->execute([
            ':name'       => $client->getName(),
            ':birth_date' => $client->getBirthDate(),
            ':cpf'        => $client->getCpf(),
            ':rg'         => $client->getRg(),
            ':phone'      => $client->getPhone()
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $client->setId($id);
        return $id;
    }

    public function findById(int $id): ?Client {
        $stmt = $this->pdo->prepare("SELECT * FROM clients WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $client = new Client(
            $row['name'],
            $row['birth_date'],
            $row['cpf'],
            $row['rg'],
            $row['phone']
        );
        $client->setId((int) $row['id']);
        return $client;
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM clients ORDER BY created_at DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $clients = [];
        foreach ($rows as $row) {
            $client = new Client(
                $row['name'],
                $row['birth_date'],
                $row['cpf'],
                $row['rg'],
                $row['phone']
            );
            $client->setId((int) $row['id']);
            $clients[] = $client;
        }
        return $clients;
    }

    public function update(Client $client): bool {
        if (!$client->getId()) {
            throw new \InvalidArgumentException("Client must have an ID to update.");
        }

        $stmt = $this->pdo->prepare(
            "UPDATE clients 
             SET name = :name, birth_date = :birth_date, cpf = :cpf, rg = :rg, phone = :phone
             WHERE id = :id"
        );

        return $stmt->execute([
            ':name'       => $client->getName(),
            ':birth_date' => $client->getBirthDate(),
            ':cpf'        => $client->getCpf(),
            ':rg'         => $client->getRg(),
            ':phone'      => $client->getPhone(),
            ':id'         => $client->getId()
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM clients WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
