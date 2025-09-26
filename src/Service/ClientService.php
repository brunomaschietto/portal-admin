<?php
namespace App\Service;

use App\Entity\Client;
use App\Repository\ClientRepository;
use InvalidArgumentException;

class ClientService {
    private ClientRepository $repository;

    public function __construct(ClientRepository $repository) {
        $this->repository = $repository;
    }

    public function createClient(array $data): int {
        if (empty($data['name']) || empty($data['birth_date']) || empty($data['cpf'])) {
            throw new InvalidArgumentException("Name, birth date and CPF are required.");
        }

        $existingClients = $this->repository->findAll();
        foreach ($existingClients as $client) {
            if ($client->getCpf() === $data['cpf']) {
                throw new InvalidArgumentException("CPF already registered.");
            }
        }

        $client = new Client(
            $data['name'],
            $data['birth_date'],
            $data['cpf'],
            $data['rg'] ?? null,
            $data['phone'] ?? null
        );

        return $this->repository->save($client);
    }

    public function getClient(int $id): ?Client {
        return $this->repository->findById($id);
    }

    public function getAllClients(): array {
        return $this->repository->findAll();
    }

    public function updateClient(int $id, array $data): bool {
        $client = $this->repository->findById($id);
        if (!$client) {
            throw new InvalidArgumentException("Client not found.");
        }

        if (!empty($data['name'])) {
            $client->setName($data['name']);
        }
        if (!empty($data['birth_date'])) {
            $client->setBirthDate($data['birth_date']);
        }
        if (!empty($data['cpf'])) {
            $client->setCpf($data['cpf']);
        }
        if (!empty($data['rg'])) {
            $client->setRg($data['rg']);
        }
        if (!empty($data['phone'])) {
            $client->setPhone($data['phone']);
        }

        return $this->repository->update($client);
    }

    public function deleteClient(int $id): bool {
        return $this->repository->delete($id);
    }
}
