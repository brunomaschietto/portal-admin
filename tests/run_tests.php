#!/usr/bin/env php
<?php

require_once __DIR__ . '/TestRunner.php';

echo "Sistema de Testes com Mocks (Sem Banco)\n";
echo "Todos os dados são simulados em memória!\n\n";

// Auto-descobrir classes de teste
$testFiles = glob(__DIR__ . '/unit/*Test.php');
$runner = new TestRunner();

foreach ($testFiles as $file) {
    require_once $file;
    $className = basename($file, '.php');
    $runner->addTest($className);
}

$runner->run();
