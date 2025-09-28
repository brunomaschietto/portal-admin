<?php
session_start();
require '/var/www/html/vendor/autoload.php';
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

$clientRepo = new ClientRepository($pdo);
$clientService = new ClientService($clientRepo);

$addressRepo = new AddressRepository($pdo);
$addressService = new AddressService($addressRepo);

$clientController = new ClientController($clientService, $addressService);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $clientData = [
            'name' => trim($_POST['name'] ?? ''),
            'birth_date' => $_POST['birth_date'] ?? '',
            'cpf' => trim($_POST['cpf'] ?? ''),
            'phone' => trim($_POST['phone'] ?? '')
        ];

        $addresses = [];
        if (isset($_POST['addresses']) && is_array($_POST['addresses'])) {
            foreach ($_POST['addresses'] as $addressData) {
                if (!empty(trim($addressData['street'] ?? ''))) {
                    $addresses[] = [
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

        $clientData['addresses'] = $addresses;

        $clientController->create($clientData);
        
        $success = "Cliente criado com sucesso!";
        
        $_POST = [];
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Adicionar Cliente</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        input:focus, select:focus { border-color: #007cba; outline: none; box-shadow: 0 0 0 2px rgba(0,124,186,0.1); }
        button { background: #007cba; color: white; padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background: #005a87; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .error { color: #dc3545; margin: 15px 0; padding: 10px; background: #f8d7da; border-radius: 4px; border: 1px solid #f5c6cb; }
        .success { color: #155724; margin: 15px 0; padding: 10px; background: #d4edda; border-radius: 4px; border: 1px solid #c3e6cb; }
        .address-section { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 6px; background: #f8f9fa; position: relative; }
        .address-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .address-number { font-weight: bold; color: #007cba; }
        .remove-address { background: #dc3545; color: white; padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .remove-address:hover { background: #c82333; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #007cba; text-decoration: none; font-weight: bold; }
        .back-link:hover { text-decoration: underline; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        h1 { margin: 0; color: #333; }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }
        .add-address-btn { margin: 15px 0; }
        .buttons-section { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; }
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
            <h1>Adicionar Novo Cliente</h1>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST" id="clientForm">
            <h2>Dados Pessoais</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Nome Completo *</label>
                    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="birth_date">Data de Nascimento</label>
                    <input type="date" id="birth_date" name="birth_date" value="<?= htmlspecialchars($_POST['birth_date'] ?? '') ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" value="<?= htmlspecialchars($_POST['cpf'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Telefone</label>
                    <input type="tel" id="phone" name="phone" placeholder="(00) 00000-0000" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
            </div>
            
            <div id="addressesContainer">
                <h2>Endereços (Opcional)</h2>
                
                <div class="address-section" data-address-index="0">
                    <div class="address-header">
                        <span class="address-number">Endereço #1</span>
                        <button type="button" class="remove-address" onclick="removeAddress(0)" style="display: none;">Remover</button>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Rua</label>
                            <input type="text" name="addresses[0][street]" value="<?= htmlspecialchars($_POST['addresses'][0]['street'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group" style="flex: 0 0 100px;">
                            <label>Número</label>
                            <input type="text" name="addresses[0][number]" value="<?= htmlspecialchars($_POST['addresses'][0]['number'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Complemento</label>
                        <input type="text" name="addresses[0][complement]" value="<?= htmlspecialchars($_POST['addresses'][0]['complement'] ?? '') ?>">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Bairro</label>
                            <input type="text" name="addresses[0][neighborhood]" value="<?= htmlspecialchars($_POST['addresses'][0]['neighborhood'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Cidade</label>
                            <input type="text" name="addresses[0][city]" value="<?= htmlspecialchars($_POST['addresses'][0]['city'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="addresses[0][state]">
                                <option value="">Selecione...</option>
                                <option value="AC" <?= ($_POST['addresses'][0]['state'] ?? '') === 'AC' ? 'selected' : '' ?>>Acre</option>
                                <option value="AL" <?= ($_POST['addresses'][0]['state'] ?? '') === 'AL' ? 'selected' : '' ?>>Alagoas</option>
                                <option value="AP" <?= ($_POST['addresses'][0]['state'] ?? '') === 'AP' ? 'selected' : '' ?>>Amapá</option>
                                <option value="AM" <?= ($_POST['addresses'][0]['state'] ?? '') === 'AM' ? 'selected' : '' ?>>Amazonas</option>
                                <option value="BA" <?= ($_POST['addresses'][0]['state'] ?? '') === 'BA' ? 'selected' : '' ?>>Bahia</option>
                                <option value="CE" <?= ($_POST['addresses'][0]['state'] ?? '') === 'CE' ? 'selected' : '' ?>>Ceará</option>
                                <option value="DF" <?= ($_POST['addresses'][0]['state'] ?? '') === 'DF' ? 'selected' : '' ?>>Distrito Federal</option>
                                <option value="ES" <?= ($_POST['addresses'][0]['state'] ?? '') === 'ES' ? 'selected' : '' ?>>Espírito Santo</option>
                                <option value="GO" <?= ($_POST['addresses'][0]['state'] ?? '') === 'GO' ? 'selected' : '' ?>>Goiás</option>
                                <option value="MA" <?= ($_POST['addresses'][0]['state'] ?? '') === 'MA' ? 'selected' : '' ?>>Maranhão</option>
                                <option value="MT" <?= ($_POST['addresses'][0]['state'] ?? '') === 'MT' ? 'selected' : '' ?>>Mato Grosso</option>
                                <option value="MS" <?= ($_POST['addresses'][0]['state'] ?? '') === 'MS' ? 'selected' : '' ?>>Mato Grosso do Sul</option>
                                <option value="MG" <?= ($_POST['addresses'][0]['state'] ?? '') === 'MG' ? 'selected' : '' ?>>Minas Gerais</option>
                                <option value="PA" <?= ($_POST['addresses'][0]['state'] ?? '') === 'PA' ? 'selected' : '' ?>>Pará</option>
                                <option value="PB" <?= ($_POST['addresses'][0]['state'] ?? '') === 'PB' ? 'selected' : '' ?>>Paraíba</option>
                                <option value="PR" <?= ($_POST['addresses'][0]['state'] ?? '') === 'PR' ? 'selected' : '' ?>>Paraná</option>
                                <option value="PE" <?= ($_POST['addresses'][0]['state'] ?? '') === 'PE' ? 'selected' : '' ?>>Pernambuco</option>
                                <option value="PI" <?= ($_POST['addresses'][0]['state'] ?? '') === 'PI' ? 'selected' : '' ?>>Piauí</option>
                                <option value="RJ" <?= ($_POST['addresses'][0]['state'] ?? '') === 'RJ' ? 'selected' : '' ?>>Rio de Janeiro</option>
                                <option value="RN" <?= ($_POST['addresses'][0]['state'] ?? '') === 'RN' ? 'selected' : '' ?>>Rio Grande do Norte</option>
                                <option value="RS" <?= ($_POST['addresses'][0]['state'] ?? '') === 'RS' ? 'selected' : '' ?>>Rio Grande do Sul</option>
                                <option value="RO" <?= ($_POST['addresses'][0]['state'] ?? '') === 'RO' ? 'selected' : '' ?>>Rondônia</option>
                                <option value="RR" <?= ($_POST['addresses'][0]['state'] ?? '') === 'RR' ? 'selected' : '' ?>>Roraima</option>
                                <option value="SC" <?= ($_POST['addresses'][0]['state'] ?? '') === 'SC' ? 'selected' : '' ?>>Santa Catarina</option>
                                <option value="SP" <?= ($_POST['addresses'][0]['state'] ?? '') === 'SP' ? 'selected' : '' ?>>São Paulo</option>
                                <option value="SE" <?= ($_POST['addresses'][0]['state'] ?? '') === 'SE' ? 'selected' : '' ?>>Sergipe</option>
                                <option value="TO" <?= ($_POST['addresses'][0]['state'] ?? '') === 'TO' ? 'selected' : '' ?>>Tocantins</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>CEP</label>
                            <input type="text" name="addresses[0][zip]" placeholder="00000-000" value="<?= htmlspecialchars($_POST['addresses'][0]['zip'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="add-address-btn">
                <button type="button" class="btn-success" onclick="addAddress()">+ Adicionar Outro Endereço</button>
            </div>
            
            <div class="buttons-section">
                <button type="submit">Salvar Cliente</button>
                <a href="list.php" class="btn-secondary" style="text-decoration: none; margin-left: 10px; display: inline-block;">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        let addressIndex = 1;

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

        function createAddressHtml(index) {
            return `
                <div class="address-section" data-address-index="${index}">
                    <div class="address-header">
                        <span class="address-number">Endereço #${index + 1}</span>
                        <button type="button" class="remove-address" onclick="removeAddress(${index})">Remover</button>
                    </div>
                    
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