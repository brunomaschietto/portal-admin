<!DOCTYPE html>
<html>
<head>
    <title><?= isset($client) ? "Edit Client" : "Add Client" ?></title>
</head>
<body>
<h1><?= isset($client) ? "Edit Client" : "Add Client" ?></h1>

<form method="post" action="">
    <label>Name: <input type="text" name="name" value="<?= $client->getName() ?? '' ?>" required></label><br>
    <label>Birth Date: <input type="date" name="birth_date" value="<?= $client->getBirthDate() ?? '' ?>" required></label><br>
    <label>CPF: <input type="text" name="cpf" value="<?= $client->getCpf() ?? '' ?>" required></label><br>
    <label>RG: <input type="text" name="rg" value="<?= $client->getRg() ?? '' ?>"></label><br>
    <label>Phone: <input type="text" name="phone" value="<?= $client->getPhone() ?? '' ?>"></label><br>

    <h3>Addresses</h3>
    <?php if (!empty($addresses)): ?>
        <?php foreach ($addresses as $i => $address): ?>
            <div>
                <label>Street: <input type="text" name="addresses[<?= $i ?>][street]" value="<?= $address->getStreet() ?>"></label>
                <label>City: <input type="text" name="addresses[<?= $i ?>][city]" value="<?= $address->getCity() ?>"></label>
                <label>State: <input type="text" name="addresses[<?= $i ?>][state]" value="<?= $address->getState() ?>"></label>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <button type="submit"><?= isset($client) ? "Update" : "Create" ?></button>
</form>

<a href="clients.php?action=list">Back to list</a>
</body>
</html>
