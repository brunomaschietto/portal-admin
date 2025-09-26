<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/db.php';

use App\Repository\ClientRepository;
use App\Repository\AddressRepository;
use App\Service\ClientService;
use App\Service\AddressService;
use App\Controller\ClientController;

$clientRepo = new ClientRepository($pdo);
$addressRepo = new AddressRepository($pdo);

$clientService = new ClientService($clientRepo);
$addressService = new AddressService($addressRepo);

$controller = new ClientController($clientService, $addressService);

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'create':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->create($_POST);
    } else {
        $client = null;
        $addresses = [];
        $controller->renderForm($client, $addresses); // vamos criar este método
    }
    break;

case 'edit':
    if (!$id) { die("Client ID required."); }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->edit((int)$id, $_POST);
    } else {
        $client = $clientService->getClient((int)$id);
        $addresses = $addressService->getAddressesByClient((int)$id);
        $controller->renderForm($client, $addresses);
    }
    break;

    case 'delete':
        if (!$id) { die("Client ID required."); }
        $controller->delete((int)$id);
        break;

    case 'list':
    default:
        $controller->list();
        break;
}
