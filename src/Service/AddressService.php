<?php
namespace App\Service;

use App\Entity\Address;
use App\Repository\AddressRepository;
use InvalidArgumentException;

class AddressService {
    private AddressRepository $repository;

    public function __construct(AddressRepository $repository) {
        $this->repository = $repository;
    }

    public function addAddress(int $clientId, array $data): int {
        if (empty($data['street']) || empty($data['city']) || empty($data['state'])) {
            throw new InvalidArgumentException("Street, city and state are required.");
        }

        $address = new Address(
            $clientId,
            $data['street'] ?? null,
            $data['number'] ?? null,
            $data['complement'] ?? null,
            $data['neighborhood'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['zip'] ?? null
        );

        return $this->repository->save($address);
    }

    public function getAddressById(int $id): ?Address {
        return $this->repository->findById($id);
    }

    public function getAddressesByClient(int $clientId): array {
        return $this->repository->findByClientId($clientId);
    }

    public function editAddress(int $clientId, array $addressData): void {
        $existingAddresses = $this->repository->findByClientId($clientId);
        
        if (!empty($existingAddresses)) {
            $address = $existingAddresses[0];
            
            $address->setStreet($addressData['street'] ?? $address->getStreet());
            $address->setNumber($addressData['number'] ?? $address->getNumber());
            $address->setComplement($addressData['complement'] ?? $address->getComplement());
            $address->setNeighborhood($addressData['neighborhood'] ?? $address->getNeighborhood());
            $address->setCity($addressData['city'] ?? $address->getCity());
            $address->setState($addressData['state'] ?? $address->getState());
            $address->setZip($addressData['zip'] ?? $address->getZip());

            $this->repository->update($address);
        }
    }

    public function deleteAddress(int $id): bool {
        return $this->repository->delete($id);
    }
}
