<?php
session_start();
require '/var/www/vendor/autoload.php';
require __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

use App\Repository\ClientRepository;
use App\Service\ClientService;
use App\Controller\ClientController;
use App\Repository\AddressRepository;
use App\Service\AddressService;


$clientRepo = new ClientRepository($pdo);
$clientService = new ClientService($clientRepo);

$addressRepo = new AddressRepository($pdo);
$addressService = new AddressService($addressRepo);

$clientController = new ClientController($clientService, $addressService);
$clients = $clientService->getAllClients();

$success = null;
if (isset($_GET['success'])) {
    $success = "Cliente criado com sucesso!";
} elseif (isset($_GET['updated'])) {
    $success = "Cliente atualizado com sucesso!";
} elseif (isset($_GET['deleted'])) {
    $success = "Cliente excluído com sucesso!";
}
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $clientId = (int)$_GET['id'];
        $clientController->delete($clientId);
        header("Location: list.php?deleted=1");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lista de Clientes</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 20px; 
            background-color: #f5f5f5;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        h1 { 
            margin: 0; 
            color: #333; 
            font-size: 28px;
        }
        
        .add-button {
            background: #007cba;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .add-button:hover {
            background: #005a87;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        
        th {
            background: #f8f9fa;
            color: #333;
            font-weight: bold;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-edit {
            background: #28a745;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn-edit:hover {
            background: #218838;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .empty-state h3 {
            margin-bottom: 10px;
            color: #333;
        }
        
        .stats {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .stats-text {
            color: #666;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }
            
            table {
                font-size: 14px;
            }
            
            th, td {
                padding: 10px 8px;
            }
            
            .actions {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Clientes</h1>
        <a href="add.php" class="add-button">+ Adicionar Cliente</a>
    </div>
    
    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($clients)): ?>
        <div class="stats">
            <span class="stats-text">
                Total de clientes: <strong><?= count($clients) ?></strong>
            </span>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Data de Nascimento</th>
                        <th>CPF</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td><?= htmlspecialchars($client->getName()) ?></td>
                            <td><?= $client->getBirthDate() ? date('d/m/Y', strtotime($client->getBirthDate())) : '-' ?></td>
                            <td><?= $client->getCpf() ?: '-' ?></td>
                            <td><?= $client->getPhone() ?: '-' ?></td>
                            <td>
                                <div class="actions">
                                    <a href="edit.php?id=<?= $client->getId() ?>" class="btn-edit">Editar</a>
                                    <a href="list.php?action=delete&id=<?= $client->getId() ?>" 
                                        class="btn-delete" 
                                        onclick="return confirm('Tem certeza que deseja excluir este cliente?')">Excluir</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="table-container">
            <div class="empty-state">
                <h3>Nenhum cliente encontrado</h3>
                <p>Que tal adicionar o primeiro cliente?</p>
                <a href="add.php" class="add-button">Adicionar Cliente</a>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>