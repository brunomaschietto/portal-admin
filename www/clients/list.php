<!DOCTYPE html>
<html>
<head>
    <title>Clients List</title>
</head>
<body>
<h1>Clients</h1>
<a href="clients.php?action=create">Add New Client</a>
<table border="1">
    <tr>
        <th>Name</th>
        <th>Birth Date</th>
        <th>CPF</th>
        <th>Phone</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($clients as $client): ?>
        <tr>
            <td><?= htmlspecialchars($client->getName()) ?></td>
            <td><?= $client->getBirthDate() ?></td>
            <td><?= $client->getCpf() ?></td>
            <td><?= $client->getPhone() ?></td>
            <td>
                <a href="clients.php?action=edit&id=<?= $client->getId() ?>">Edit</a>
                <a href="clients.php?action=delete&id=<?= $client->getId() ?>" onclick="return confirm('Delete?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
