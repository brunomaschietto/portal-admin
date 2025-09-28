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

        // Processar múltiplos endereços
        $addressesData = [];
        if (isset($_POST['addresses']) && is_array($_POST['addresses'])) {
            foreach ($_POST['addresses'] as $addressData) {
                // Só adiciona o endereço se pelo menos a rua estiver preenchida
                if (!empty(trim($addressData['street'] ?? ''))) {
                    $addressesData[] = [
                        'id' => $addressData['id'] ?? null, // ID para identificar endereços existentes
                        'street' => trim($addressData['street']),
                        'number' => trim($addressData['number'] ?? ''),
                        'complement' => trim($addressData['complement'] ?? ''),
                        'neighborhood' => trim($addressData['neighborhood'] ?? ''),
                        'city' => trim($addressData['city'] ?? ''),
                        'state' => trim($addressData['state'] ?? ''),
                        'zip' => trim($addressData['zip'] ?? '')
                    ];
                }
            }
        }

        $clientData['addresses'] = $addressesData;

        $clientController->edit($clientId, $clientData);
        
        $success = "Cliente atualizado com sucesso!";
        
        // Recarregar dados atualizados
        $client = $clientService->getClient($clientId);
        $addresses = $addressService->getAddressesByClient($clientId);
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

if (!$client && !$error) {
    $error = "Cliente não encontrado.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Editar Cliente</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px; 
            background-color: #f5f5f5; 
        }
        .container { 
            background: white; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: bold; 
            color: #333; 
        }
        input, select, textarea { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            box-sizing: border-box; 
            font-size: 14px; 
        }
        input:focus, select:focus { 
            border-color: #007cba; 
            outline: none; 
            box-shadow: 0 0 0 2px rgba(0,124,186,0.1); 
        }
        button { 
            background: #007cba; 
            color: white; 
            padding: 12px 20px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: bold; 
        }
        button:hover { 
            background: #005a87; 
        }
        .btn-secondary { 
            background: #6c757d; 
            color: white; 
            padding: 12px 20px; 
            text-decoration: none; 
            border-radius: 6px; 
            margin-left: 10px; 
            display: inline-block; 
        }
        .btn-secondary:hover { 
            background: #5a6268; 
        }
        .btn-danger { 
            background: #dc3545; 
        }
        .btn-danger:hover { 
            background: #c82333; 
        }
        .btn-success { 
            background: #28a745; 
        }
        .btn-success:hover { 
            background: #218838; 
        }
        .error { 
            color: #dc3545; 
            margin: 15px 0; 
            padding: 10px; 
            background: #f8d7da; 
            border-radius: 4px; 
            border: 1px solid #f5c6cb; 
        }
        .success { 
            color: #155724; 
            margin: 15px 0; 
            padding: 10px; 
            background: #d4edda; 
            border-radius: 4px; 
            border: 1px solid #c3e6cb; 
        }
        .address-section { 
            border: 1px solid #ddd; 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 6px; 
            background: #f8f9fa; 
            position: relative; 
        }
        .address-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 15px; 
        }
        .address-number { 
            font-weight: bold; 
            color: #007cba; 
        }
        .remove-address { 
            background: #dc3545; 
            color: white; 
            padding: 8px 12px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 12px; 
        }
        .remove-address:hover { 
            background: #c82333; 
        }
        .back-link { 
            display: inline-block; 
            margin-bottom: 20px; 
            color: #007cba; 
            text-decoration: none; 
            font-weight: bold; 
        }
        .back-link:hover { 
            text-decoration: underline; 
        }
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px; 
        }
        h1 { 
            margin: 0; 
            color: #333; 
        }
        .form-row { 
            display: flex; 
            gap: 15px; 
        }
        .form-row .form-group { 
            flex: 1; 
        }
        .add-address-btn { 
            margin: 15px 0; 
        }
        .buttons-section { 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 1px solid #eee; 
        }
        @media (max-width: 768px) { 
            .form-row { 
                flex-direction: column; 
                gap: 0; 
            }
            .header { 
                flex-direction: column; 
                gap: 15px; 
                align-items: flex-start; 
            }
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
            <form method="POST" id="clientForm">
                <h2 style="color: #333; border-bottom: 2px solid #007cba; padding-bottom: 10px;">Dados Pessoais</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nome Completo *</label>
                        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($client->getName()) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="birth_date">Data de Nascimento</label>
                        <input type="date" id="birth_date" name="birth_date" value="<?= htmlspecialchars($client->getBirthDate() ?: '') ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="cpf">CPF</label>
                        <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" value="<?= htmlspecialchars($client->getCpf() ?: '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Telefone</label>
                        <input type="tel" id="phone" name="phone" placeholder="(00) 00000-0000" value="<?= htmlspecialchars($client->getPhone() ?: '') ?>">
                    </div>
                </div>
                
                <div id="addressesContainer">
                    <h2 style="color: #333; border-bottom: 2px solid #007cba; padding-bottom: 10px;">Endereços</h2>
                    
                    <?php if (empty($addresses)): ?>
                        <!-- Se não há endereços, criar um vazio -->
                        <div class="address-section" data-address-index="0">
                            <div class="address-header">
                                <span class="address-number">Endereço #1</span>
                                <button type="button" class="remove-address" onclick="removeAddress(0)" style="display: none;">Remover</button>
                            </div>
                            
                            <input type="hidden" name="addresses[0][id]" value="">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Rua</label>
                                    <input type="text" name="addresses[0][street]" value="">
                                </div>
                                
                                <div class="form-group" style="flex: 0 0 100px;">
                                    <label>Número</label>
                                    <input type="text" name="addresses[0][number]" value="">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Complemento</label>
                                <input type="text" name="addresses[0][complement]" value="">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Bairro</label>
                                    <input type="text" name="addresses[0][neighborhood]" value="">
                                </div>
                                
                                <div class="form-group">
                                    <label>Cidade</label>
                                    <input type="text" name="addresses[0][city]" value="">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Estado</label>
                                    <select name="addresses[0][state]">
                                        <option value="">Selecione...</option>
                                        <option value="AC">Acre</option>
                                        <option value="AL">Alagoas</option>
                                        <option value="AP">Amapá</option>
                                        <option value="AM">Amazonas</option>
                                        <option value="BA">Bahia</option>
                                        <option value="CE">Ceará</option>
                                        <option value="DF">Distrito Federal</option>
                                        <option value="ES">Espírito Santo</option>
                                        <option value="GO">Goiás</option>
                                        <option value="MA">Maranhão</option>
                                        <option value="MT">Mato Grosso</option>
                                        <option value="MS">Mato Grosso do Sul</option>
                                        <option value="MG">Minas Gerais</option>
                                        <option value="PA">Pará</option>
                                        <option value="PB">Paraíba</option>
                                        <option value="PR">Paraná</option>
                                        <option value="PE">Pernambuco</option>
                                        <option value="PI">Piauí</option>
                                        <option value="RJ">Rio de Janeiro</option>
                                        <option value="RN">Rio Grande do Norte</option>
                                        <option value="RS">Rio Grande do Sul</option>
                                        <option value="RO">Rondônia</option>
                                        <option value="RR">Roraima</option>
                                        <option value="SC">Santa Catarina</option>
                                        <option value="SP">São Paulo</option>
                                        <option value="SE">Sergipe</option>
                                        <option value="TO">Tocantins</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>CEP</label>
                                    <input type="text" name="addresses[0][zip]" placeholder="00000-000" value="">
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Renderizar endereços existentes -->
                        <?php foreach ($addresses as $index => $address): ?>
                            <div class="address-section" data-address-index="<?= $index ?>">
                                <div class="address-header">
                                    <span class="address-number">Endereço #<?= $index + 1 ?></span>
                                    <button type="button" class="remove-address" onclick="removeAddress(<?= $index ?>)">Remover</button>
                                </div>
                                
                                <input type="hidden" name="addresses[<?= $index ?>][id]" value="<?= $address->getId() ?>">
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Rua</label>
                                        <input type="text" name="addresses[<?= $index ?>][street]" value="<?= htmlspecialchars($address->getStreet()) ?>">
                                    </div>
                                    
                                    <div class="form-group" style="flex: 0 0 100px;">
                                        <label>Número</label>
                                        <input type="text" name="addresses[<?= $index ?>][number]" value="<?= htmlspecialchars($address->getNumber() ?? '') ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Complemento</label>
                                    <input type="text" name="addresses[<?= $index ?>][complement]" value="<?= htmlspecialchars($address->getComplement() ?? '') ?>">
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Bairro</label>
                                        <input type="text" name="addresses[<?= $index ?>][neighborhood]" value="<?= htmlspecialchars($address->getNeighborhood() ?? '') ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Cidade</label>
                                        <input type="text" name="addresses[<?= $index ?>][city]" value="<?= htmlspecialchars($address->getCity() ?? '') ?>">
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select name="addresses[<?= $index ?>][state]">
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
                                            foreach ($states as $code => $name): ?>
                                                <option value="<?= $code ?>" <?= ($address->getState() === $code) ? 'selected' : '' ?>><?= $name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>CEP</label>
                                        <input type="text" name="addresses[<?= $index ?>][zip]" placeholder="00000-000" value="<?= htmlspecialchars($address->getZip() ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="add-address-btn">
                    <button type="button" class="btn-success" onclick="addAddress()">+ Adicionar Outro Endereço</button>
                </div>
                
                <div class="buttons-section">
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

    <script>
        let addressIndex = <?= count($addresses) > 0 ? count($addresses) : 1 ?>;

        function addAddress() {
            const container = document.getElementById('addressesContainer');
            const newAddressHtml = createAddressHtml(addressIndex);
            container.insertAdjacentHTML('beforeend', newAddressHtml);
            
            updateRemoveButtons();
            addressIndex++;
        }

        function removeAddress(index) {
            const addressSection = document.querySelector(`[data-address-index="${index}"]`);
            if (addressSection) {
                addressSection.remove();
                updateRemoveButtons();
                reorderAddresses();
            }
        }

        function updateRemoveButtons() {
            const addressSections = document.querySelectorAll('.address-section');
            addressSections.forEach((section, index) => {
                const removeBtn = section.querySelector('.remove-address');
                if (addressSections.length > 1) {
                    removeBtn.style.display = 'inline-block';
                } else {
                    removeBtn.style.display = 'none';
                }
            });
        }

        function reorderAddresses() {
            const addressSections = document.querySelectorAll('.address-section');
            addressSections.forEach((section, index) => {
                const numberSpan = section.querySelector('.address-number');
                numberSpan.textContent = `Endereço #${index + 1}`;
            });
        }

        function createAddressHtml(index) {
            return `
                <div class="address-section" data-address-index="${index}">
                    <div class="address-header">
                        <span class="address-number">Endereço #${index + 1}</span>
                        <button type="button" class="remove-address" onclick="removeAddress(${index})">Remover</button>
                    </div>
                    
                    <input type="hidden" name="addresses[${index}][id]" value="">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Rua</label>
                            <input type="text" name="addresses[${index}][street]">
                        </div>
                        
                        <div class="form-group" style="flex: 0 0 100px;">
                            <label>Número</label>
                            <input type="text" name="addresses[${index}][number]">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Complemento</label>
                        <input type="text" name="addresses[${index}][complement]">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Bairro</label>
                            <input type="text" name="addresses[${index}][neighborhood]">
                        </div>
                        
                        <div class="form-group">
                            <label>Cidade</label>
                            <input type="text" name="addresses[${index}][city]">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="addresses[${index}][state]">
                                <option value="">Selecione...</option>
                                <option value="AC">Acre</option>
                                <option value="AL">Alagoas</option>
                                <option value="AP">Amapá</option>
                                <option value="AM">Amazonas</option>
                                <option value="BA">Bahia</option>
                                <option value="CE">Ceará</option>
                                <option value="DF">Distrito Federal</option>
                                <option value="ES">Espírito Santo</option>
                                <option value="GO">Goiás</option>
                                <option value="MA">Maranhão</option>
                                <option value="MT">Mato Grosso</option>
                                <option value="MS">Mato Grosso do Sul</option>
                                <option value="MG">Minas Gerais</option>
                                <option value="PA">Pará</option>
                                <option value="PB">Paraíba</option>
                                <option value="PR">Paraná</option>
                                <option value="PE">Pernambuco</option>
                                <option value="PI">Piauí</option>
                                <option value="RJ">Rio de Janeiro</option>
                                <option value="RN">Rio Grande do Norte</option>
                                <option value="RS">Rio Grande do Sul</option>
                                <option value="RO">Rondônia</option>
                                <option value="RR">Roraima</option>
                                <option value="SC">Santa Catarina</option>
                                <option value="SP">São Paulo</option>
                                <option value="SE">Sergipe</option>
                                <option value="TO">Tocantins</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>CEP</label>
                            <input type="text" name="addresses[${index}][zip]" placeholder="00000-000">
                        </div>
                    </div>
                </div>
            `;
        }

        updateRemoveButtons();
    </script>
</body>
</html>