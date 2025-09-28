<?php

class TestRunner
{
    private array $tests = [];
    private array $results = [];
    private int $passed = 0;
    private int $failed = 0;
    private float $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    public function addTest(string $testClass): void
    {
        $this->tests[] = $testClass;
    }

    public function run(): void
    {
        echo "\n Executando Testes Automatizados (Com Mocks)...\n";
        echo "==================================================\n\n";

        foreach ($this->tests as $testClass) {
            $this->runTestClass($testClass);
        }

        $this->printSummary();
    }

    private function runTestClass(string $testClass): void
    {
        if (!class_exists($testClass)) {
            echo " Classe de teste não encontrada: $testClass\n";
            return;
        }

        $reflection = new ReflectionClass($testClass);
        $instance = $reflection->newInstance();
        
        echo " Executando: " . $reflection->getShortName() . "\n";

        // Executar setUp se existir
        if (method_exists($instance, 'setUp')) {
            $instance->setUp();
        }

        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        
        foreach ($methods as $method) {
            if (strpos($method->getName(), 'test') === 0) {
                $this->runTest($instance, $method->getName(), $testClass);
            }
        }

        // Executar tearDown se existir
        if (method_exists($instance, 'tearDown')) {
            $instance->tearDown();
        }

        echo "\n";
    }

    private function runTest($instance, string $methodName, string $className): void
    {
        try {
            $instance->$methodName();
            echo "   $methodName\n";
            $this->passed++;
            $this->results[] = [
                'class' => $className,
                'method' => $methodName,
                'status' => 'PASSED',
                'message' => null
            ];
        } catch (AssertionException $e) {
            echo "   $methodName - {$e->getMessage()}\n";
            $this->failed++;
            $this->results[] = [
                'class' => $className,
                'method' => $methodName,
                'status' => 'FAILED',
                'message' => $e->getMessage()
            ];
        } catch (Exception $e) {
            echo "   $methodName - Erro: {$e->getMessage()}\n";
            $this->failed++;
            $this->results[] = [
                'class' => $className,
                'method' => $methodName,
                'status' => 'ERROR',
                'message' => $e->getMessage()
            ];
        }
    }

    private function printSummary(): void
    {
        $total = $this->passed + $this->failed;
        $time = round(microtime(true) - $this->startTime, 2);

        echo "=====================================\n";
        echo " Resumo dos Testes\n";
        echo "=====================================\n";
        echo "Total: $total\n";
        echo " Passou: {$this->passed}\n";
        echo " Falhou: {$this->failed}\n";
        echo " Tempo: {$time}s\n";

        if ($this->failed > 0) {
            echo "\n🔍 Detalhes dos Falhas:\n";
            foreach ($this->results as $result) {
                if ($result['status'] !== 'PASSED') {
                    echo "  - {$result['class']}::{$result['method']}: {$result['message']}\n";
                }
            }
        }

        if ($this->failed === 0) {
            echo "\n Todos os testes passaram! (100% mockados)\n";
            exit(0);
        } else {
            echo "\n Alguns testes falharam!\n";
            exit(1);
        }
    }
}
