<?php
echo "Configurando banco de dados de teste...\n";

try {
    // Conectar ao MySQL sem especificar banco
    $pdo = new PDO(
        "mysql:host=localhost",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Criar banco se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS test_database");
    echo "Banco de dados 'test_database' criado/verificado\n";

    // Conectar ao banco específico
    $pdo = new PDO(
        "mysql:host=localhost;dbname=test_database",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Criar tabelas
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS clients (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            birth_date DATE,
            cpf VARCHAR(14),
            phone VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS addresses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_id INT NOT NULL,
            street VARCHAR(255) NOT NULL,
            number VARCHAR(10),
            complement VARCHAR(100),
            neighborhood VARCHAR(100),
            city VARCHAR(100),
            state VARCHAR(2),
            zip VARCHAR(9),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(150),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    echo "Tabelas criadas com sucesso\n";

} catch (PDOException $e) {
    echo "Erro ao configurar banco: " . $e->getMessage() . "\n";
    exit(1);
}
