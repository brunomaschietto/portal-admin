<?php
namespace App\Controller;

use App\Service\ClientService;
use App\Entity\Client;
use App\Service\AddressService;
use InvalidArgumentException;

class ClientController {
    private ClientService $clientService;
    private AddressService $addressService;

    public function __construct(ClientService $clientService, AddressService $addressService) {
        $this->clientService = $clientService;
        $this->addressService = $addressService;
    }

    public function create(array $data): void {
        try {
            $clientId = $this->clientService->createClient($data);

            if (!empty($data['addresses']) && is_array($data['addresses'])) {
                foreach ($data['addresses'] as $addressData) {
                    $this->addressService->addAddress($clientId, $addressData);
                }
            }

            header("Location: /clients/list.php?success=1");
            exit;
        } catch (InvalidArgumentException $e) {
            echo "Validation error: " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Unexpected error: " . $e->getMessage();
        }
    }

    public function list(): void {
        $clients = $this->clientService->getAllClients();
        include __DIR__ . "/../../views/clients/list.php"; 
    }

    public function edit(int $id, array $data): void {
        try {
            $this->clientService->updateClient($id, $data);
            header("Location: /clients/list.php?updated=1");
            exit;
        } catch (\Exception $e) {
            echo "Error updating client: " . $e->getMessage();
        }
    }

    public function delete(int $id): void {
        $this->clientService->deleteClient($id);
        header("Location: /clients/list.php?deleted=1");
        exit;
    }

    public function renderForm(?Client $client, array $addresses = []): void {
        include __DIR__ . "/../../views/clients/form.php";
    }
}
