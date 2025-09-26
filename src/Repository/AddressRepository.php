<?php
namespace App\Repository;

use PDO;
use App\Entity\Address;

class AddressRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function save(Address $address): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO addresses (client_id, street, number, complement, neighborhood, city, state, zip, created_at) 
             VALUES (:client_id, :street, :number, :complement, :neighborhood, :city, :state, :zip, :created_at)"
        );

        $stmt->execute([
            ':client_id'   => $address->getClientId(),
            ':street'      => $address->getStreet(),
            ':number'      => $address->getNumber(),
            ':complement'  => $address->getComplement(),
            ':neighborhood'=> $address->getNeighborhood(),
            ':city'        => $address->getCity(),
            ':state'       => $address->getState(),
            ':zip'         => $address->getZip(),
            ':created_at'  => $address->getCreatedAt()
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $address->setId($id);
        return $id;
    }

    public function findById(int $id): ?Address {
        $stmt = $this->pdo->prepare("SELECT * FROM addresses WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $address = new Address(
            (int) $row['client_id'],
            $row['street'],
            $row['number'],
            $row['complement'],
            $row['neighborhood'],
            $row['city'],
            $row['state'],
            $row['zip']
        );
        $address->setId((int) $row['id']);
        $address->setCreatedAt($row['created_at']);
        return $address;
    }

    public function findByClientId(int $clientId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM addresses WHERE client_id = :client_id");
        $stmt->execute([':client_id' => $clientId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $addresses = [];
        foreach ($rows as $row) {
            $address = new Address(
                (int) $row['client_id'],
                $row['street'],
                $row['number'],
                $row['complement'],
                $row['neighborhood'],
                $row['city'],
                $row['state'],
                $row['zip']
            );
            $address->setId((int) $row['id']);
            $address->setCreatedAt($row['created_at']);
            $addresses[] = $address;
        }
        return $addresses;
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM addresses WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
