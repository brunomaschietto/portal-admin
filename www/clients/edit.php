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

$error = null;
$success = null;
$client = null;
$addresses = [];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: list.php');
    exit();
}

$clientId = (int)$_GET['id'];

$clientRepo = new ClientRepository($pdo);
$clientService = new ClientService($clientRepo);

$addressRepo = new AddressRepository($pdo);
$addressService = new AddressService($addressRepo);

$clientController = new ClientController($clientService, $addressService);

try {

    $client = $clientService->getClient($clientId);
    if (!$client) {
        $error = "Cliente não encontrado.";
    } else {
    
        try {
            $addresses = $addressService->getAddressesByClient($clientId);
        } catch (Exception $e) {
        
            $addresses = [];
        }
    }
} catch (Exception $e) {
    $error = "Erro ao carregar dados do cliente: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $client) {
    try {
    
        $clientData = [
            'name' => trim($_POST['name'] ?? ''),
            'birth_date' => $_POST['birth_date'] ?? '',
            'cpf' => trim($_POST['cpf'] ?? ''),
            'phone' => trim($_POST['phone'] ?? '')
        ];

    
        $addressesData = [];
        if (!empty($_POST['address_street'])) {
            $addressesData[] = [
                'street' => trim($_POST['address_street']),
                'number' => trim($_POST['address_number'] ?? ''),
                'complement' => trim($_POST['address_complement'] ?? ''),
                'neighborhood' => trim($_POST['address_neighborhood'] ?? ''),
                'city' => trim($_POST['address_city'] ?? ''),
                'state' => trim($_POST['address_state'] ?? ''),
                'zip' => trim($_POST['address_zip'] ?? '')
            ];
        }

        $clientData['addresses'] = $addressesData;

    
        $clientController->edit($clientId, $clientData);
        
    
        $success = "Cliente atualizado com sucesso!";
        
    
        $client = $clientService->getClient($clientId);
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

if (!$client && !$error) {
    $error = "Cliente não encontrado.";
}

$address = !empty($addresses) ? $addresses[0] : null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Editar Cliente</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        input:focus, select:focus { border-color: #007cba; outline: none; box-shadow: 0 0 0 2px rgba(0,124,186,0.1); }
        button { background: #007cba; color: white; padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background: #005a87; }
        .error { color: #dc3545; margin: 15px 0; padding: 10px; background: #f8d7da; border-radius: 4px; border: 1px solid #f5c6cb; }
        .success { color: #155724; margin: 15px 0; padding: 10px; background: #d4edda; border-radius: 4px; border: 1px solid #c3e6cb; }
        .address-section { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 6px; background: #f8f9fa; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #007cba; text-decoration: none; font-weight: bold; }
        .back-link:hover { text-decoration: underline; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        h1 { margin: 0; color: #333; }
        .btn-secondary { background: #6c757d; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; margin-left: 10px; display: inline-block; }
        .btn-secondary:hover { background: #5a6268; }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }
        @media (max-width: 768px) { 
            .form-row { flex-direction: column; gap: 0; }
            .header { flex-direction: column; gap: 15px; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="list.php" class="back-link">← Voltar para Lista de Clientes</a>
        
        <div class="header">
            <h1>Editar Cliente</h1>
            <?php if ($client): ?>
                <small style="color: #666;">ID: <?= $client->getId() ?></small>
            <?php endif; ?>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($client): ?>
            <form method="POST">
                <h2 style="color: #333; border-bottom: 2px solid #007cba; padding-bottom: 10px;">Dados Pessoais</h2>
                
                <div class="form-group">
                    <label for="name">Nome Completo *</label>
                    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($client->getName()) ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="birth_date">Data de Nascimento</label>
                        <input type="date" id="birth_date" name="birth_date" value="<?= htmlspecialchars($client->getBirthDate() ?: '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="cpf">CPF</label>
                        <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" value="<?= htmlspecialchars($client->getCpf() ?: '') ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Telefone</label>
                        <input type="tel" id="phone" name="phone" placeholder="(00) 00000-0000" value="<?= htmlspecialchars($client->getPhone() ?: '') ?>">
                    </div>
                </div>
                
                <div class="address-section">
                    <h2 style="margin-top: 0; color: #333;">Endereço</h2>
                    
                    <div class="form-row">
                        <div class="form-group" style="flex: 3;">
                            <label for="address_street">Rua</label>
                            <input type="text" id="address_street" name="address_street" value="<?= htmlspecialchars($address ? $address->getStreet() : '') ?>">
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label for="address_number">Número</label>
                            <input type="text" id="address_number" name="address_number" value="<?= htmlspecialchars($address ? $address->getNumber() : '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address_complement">Complemento</label>
                        <input type="text" id="address_complement" name="address_complement" value="<?= htmlspecialchars($address ? $address->getComplement() : '') ?>">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="address_neighborhood">Bairro</label>
                            <input type="text" id="address_neighborhood" name="address_neighborhood" value="<?= htmlspecialchars($address ? $address->getNeighborhood() : '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="address_city">Cidade</label>
                            <input type="text" id="address_city" name="address_city" value="<?= htmlspecialchars($address ? $address->getCity() : '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="address_state">Estado</label>
                            <select id="address_state" name="address_state">
                                <option value="">Selecione...</option>
                                <?php 
                                $states = [
                                    'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas', 'BA' => 'Bahia',
                                    'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo', 'GO' => 'Goiás',
                                    'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul', 'MG' => 'Minas Gerais',
                                    'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná', 'PE' => 'Pernambuco', 'PI' => 'Piauí',
                                    'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte', 'RS' => 'Rio Grande do Sul',
                                    'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina', 'SP' => 'São Paulo',
                                    'SE' => 'Sergipe', 'TO' => 'Tocantins'
                                ];
                                $selectedState = $address ? $address->getState() : '';
                                foreach ($states as $code => $name): ?>
                                    <option value="<?= $code ?>" <?= $selectedState === $code ? 'selected' : '' ?>><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="address_zip_code">CEP</label>
                            <input type="text" id="address_zip_code" name="address_zip" placeholder="00000-000" value="<?= htmlspecialchars($address ? $address->getZip() ?? '' : '') ?>">
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 30px;">
                    <button type="submit">Atualizar Cliente</button>
                    <a href="list.php" class="btn-secondary">Cancelar</a>
                </div>
            </form>
        <?php else: ?>
            <div style="text-align: center; padding: 40px;">
                <h3 style="color: #666;">Cliente não encontrado</h3>
                <a href="list.php" class="btn-secondary">Voltar para Lista</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>