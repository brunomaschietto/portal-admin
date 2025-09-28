#!/bin/bash

echo " Configurando Sistema de Testes com Mocks (Sem Banco)..."
echo "==========================================================="

# Criar estrutura de diretórios
echo " Criando estrutura de diretórios..."
mkdir -p tests/{unit,integration,feature,mocks}

# Criar TestRunner.php
echo " Criando TestRunner..."
cat > tests/TestRunner.php << 'EOF'
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
EOF

# Criar Assert.php
echo "Criando Assert..."
cat > tests/Assert.php << 'EOF'
<?php

class AssertionException extends Exception {}

class Assert
{
    public static function assertTrue(bool $condition, string $message = 'Assertion failed'): void
    {
        if (!$condition) {
            throw new AssertionException("$message: Expected true, got false");
        }
    }

    public static function assertFalse(bool $condition, string $message = 'Assertion failed'): void
    {
        if ($condition) {
            throw new AssertionException("$message: Expected false, got true");
        }
    }

    public static function assertEquals($expected, $actual, string $message = 'Assertion failed'): void
    {
        if ($expected !== $actual) {
            $expectedStr = var_export($expected, true);
            $actualStr = var_export($actual, true);
            throw new AssertionException("$message: Expected $expectedStr, got $actualStr");
        }
    }

    public static function assertNotEquals($expected, $actual, string $message = 'Assertion failed'): void
    {
        if ($expected === $actual) {
            $expectedStr = var_export($expected, true);
            throw new AssertionException("$message: Expected not to be $expectedStr");
        }
    }

    public static function assertNull($actual, string $message = 'Assertion failed'): void
    {
        if ($actual !== null) {
            $actualStr = var_export($actual, true);
            throw new AssertionException("$message: Expected null, got $actualStr");
        }
    }

    public static function assertNotNull($actual, string $message = 'Assertion failed'): void
    {
        if ($actual === null) {
            throw new AssertionException("$message: Expected not null, got null");
        }
    }

    public static function assertInstanceOf(string $expected, $actual, string $message = 'Assertion failed'): void
    {
        if (!($actual instanceof $expected)) {
            $actualType = is_object($actual) ? get_class($actual) : gettype($actual);
            throw new AssertionException("$message: Expected instance of $expected, got $actualType");
        }
    }

    public static function assertContains($needle, array $haystack, string $message = 'Assertion failed'): void
    {
        if (!in_array($needle, $haystack)) {
            throw new AssertionException("$message: Array does not contain expected value");
        }
    }

    public static function assertCount(int $expected, array $actual, string $message = 'Assertion failed'): void
    {
        $actualCount = count($actual);
        if ($actualCount !== $expected) {
            throw new AssertionException("$message: Expected count $expected, got $actualCount");
        }
    }

    public static function assertStringContains(string $needle, string $haystack, string $message = 'Assertion failed'): void
    {
        if (strpos($haystack, $needle) === false) {
            throw new AssertionException("$message: String '$haystack' does not contain '$needle'");
        }
    }

    public static function expectException(string $exceptionClass, callable $callback, string $message = 'Expected exception was not thrown'): void
    {
        try {
            $callback();
            throw new AssertionException("$message: Expected $exceptionClass to be thrown");
        } catch (Exception $e) {
            if (!($e instanceof $exceptionClass)) {
                throw new AssertionException("$message: Expected $exceptionClass, got " . get_class($e));
            }
        }
    }
}
EOF

# Criar BaseTestCase.php (sem banco)
echo "Criando BaseTestCase (sem banco)..."
cat > tests/BaseTestCase.php << 'EOF'
<?php

abstract class BaseTestCase
{
    protected FakeDataGenerator $faker;
    protected array $mockData = [];

    public function setUp(): void
    {
        $this->faker = new FakeDataGenerator();
        $this->mockData = [
            'clients' => [],
            'addresses' => [],
            'users' => []
        ];
    }

    public function tearDown(): void
    {
        $this->mockData = [];
    }

    // Simular criação de cliente em memória
    protected function createTestClient(array $data = []): array
    {
        static $clientId = 1;
        
        $clientData = array_merge([
            'id' => $clientId++,
            'name' => $this->faker->name(),
            'birth_date' => $this->faker->date(),
            'cpf' => $this->faker->cpf(),
            'phone' => $this->faker->phone(),
            'created_at' => date('Y-m-d H:i:s')
        ], $data);

        $this->mockData['clients'][] = $clientData;
        return $clientData;
    }

    // Simular criação de endereço em memória
    protected function createTestAddress(int $clientId, array $data = []): array
    {
        static $addressId = 1;
        
        $addressData = array_merge([
            'id' => $addressId++,
            'client_id' => $clientId,
            'street' => $this->faker->street(),
            'number' => $this->faker->number(),
            'complement' => $this->faker->complement(),
            'neighborhood' => $this->faker->neighborhood(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'zip' => $this->faker->zip(),
            'created_at' => date('Y-m-d H:i:s')
        ], $data);

        $this->mockData['addresses'][] = $addressData;
        return $addressData;
    }

    // Simular busca de cliente
    protected function findClientById(int $id): ?array
    {
        foreach ($this->mockData['clients'] as $client) {
            if ($client['id'] === $id) {
                return $client;
            }
        }
        return null;
    }

    // Simular busca de endereços por client_id
    protected function findAddressesByClientId(int $clientId): array
    {
        return array_filter($this->mockData['addresses'], function($address) use ($clientId) {
            return $address['client_id'] === $clientId;
        });
    }
}
EOF

# Criar FakeDataGenerator.php
echo "Criando FakeDataGenerator..."
cat > tests/FakeDataGenerator.php << 'EOF'
<?php

class FakeDataGenerator
{
    private array $names = [
        'João Silva', 'Maria Santos', 'Pedro Oliveira', 'Ana Costa', 'Carlos Ferreira',
        'Lucia Pereira', 'Fernando Lima', 'Julia Almeida', 'Ricardo Souza', 'Amanda Ribeiro'
    ];

    private array $streets = [
        'Rua das Flores', 'Av. Paulista', 'Rua Augusta', 'Rua da Consolação', 'Av. Brasil',
        'Rua Voluntários da Pátria', 'Av. Faria Lima', 'Rua Oscar Freire', 'Rua 25 de Março'
    ];

    private array $neighborhoods = [
        'Centro', 'Vila Madalena', 'Jardins', 'Mooca', 'Santana', 'Vila Nova Conceição',
        'Bela Vista', 'Liberdade', 'Brooklin', 'Itaim Bibi'
    ];

    private array $cities = [
        'São Paulo', 'Rio de Janeiro', 'Belo Horizonte', 'Brasília', 'Curitiba',
        'Porto Alegre', 'Salvador', 'Recife', 'Fortaleza', 'Manaus'
    ];

    private array $states = ['SP', 'RJ', 'MG', 'RS', 'PR', 'SC', 'BA', 'GO', 'PE', 'CE'];

    public function name(): string
    {
        return $this->names[array_rand($this->names)];
    }

    public function date(): string
    {
        $year = rand(1950, 2005);
        $month = rand(1, 12);
        $day = rand(1, 28);
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    public function cpf(): string
    {
        return sprintf('%03d.%03d.%03d-%02d', 
            rand(100, 999), rand(100, 999), rand(100, 999), rand(10, 99));
    }

    public function phone(): string
    {
        return sprintf('(%02d) %d%04d-%04d', 
            rand(11, 85), rand(8, 9), rand(1000, 9999), rand(1000, 9999));
    }

    public function street(): string
    {
        return $this->streets[array_rand($this->streets)];
    }

    public function number(): string
    {
        return (string)rand(1, 9999);
    }

    public function complement(): ?string
    {
        $complements = [null, 'Apto 101', 'Casa 2', 'Sobrado', 'Bloco A', 'Casa dos Fundos'];
        return $complements[array_rand($complements)];
    }

    public function neighborhood(): string
    {
        return $this->neighborhoods[array_rand($this->neighborhoods)];
    }

    public function city(): string
    {
        return $this->cities[array_rand($this->cities)];
    }

    public function state(): string
    {
        return $this->states[array_rand($this->states)];
    }

    public function zip(): string
    {
        return sprintf('%05d-%03d', rand(10000, 99999), rand(100, 999));
    }

    public function email(): string
    {
        $domains = ['gmail.com', 'hotmail.com', 'yahoo.com', 'outlook.com'];
        $name = strtolower(str_replace(' ', '.', $this->name()));
        return $name . '@' . $domains[array_rand($domains)];
    }
}
EOF

# Criar MockRepository - Repositório falso em memória
echo "Criando MockRepository..."
cat > tests/mocks/MockRepository.php << 'EOF'
<?php

class MockRepository
{
    protected array $data = [];
    protected int $nextId = 1;
    protected string $tableName;

    public function __construct(string $tableName = 'mock_table')
    {
        $this->tableName = $tableName;
    }

    // Simular inserção
    public function insert(array $data): int
    {
        $data['id'] = $this->nextId++;
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->data[] = $data;
        return $data['id'];
    }

    // Simular busca por ID
    public function findById(int $id): ?array
    {
        foreach ($this->data as $record) {
            if ($record['id'] === $id) {
                return $record;
            }
        }
        return null;
    }

    // Simular busca todos
    public function findAll(): array
    {
        return $this->data;
    }

    // Simular atualização
    public function update(int $id, array $newData): bool
    {
        foreach ($this->data as $index => $record) {
            if ($record['id'] === $id) {
                $this->data[$index] = array_merge($record, $newData);
                return true;
            }
        }
        return false;
    }

    // Simular exclusão
    public function delete(int $id): bool
    {
        foreach ($this->data as $index => $record) {
            if ($record['id'] === $id) {
                unset($this->data[$index]);
                $this->data = array_values($this->data); // Reindexar
                return true;
            }
        }
        return false;
    }

    // Simular busca com condições
    public function findWhere(string $column, $value): array
    {
        return array_filter($this->data, function($record) use ($column, $value) {
            return isset($record[$column]) && $record[$column] === $value;
        });
    }

    // Limpar dados
    public function clear(): void
    {
        $this->data = [];
        $this->nextId = 1;
    }

    // Contar registros
    public function count(): int
    {
        return count($this->data);
    }
}
EOF

# Criar exemplo de teste COM MOCKS
echo "Criando exemplos de teste com mocks..."
cat > tests/unit/ExampleMockTest.php << 'EOF'
<?php

require_once __DIR__ . '/../TestRunner.php';
require_once __DIR__ . '/../Assert.php';
require_once __DIR__ . '/../BaseTestCase.php';
require_once __DIR__ . '/../FakeDataGenerator.php';
require_once __DIR__ . '/../mocks/MockRepository.php';

class ExampleMockTest extends BaseTestCase
{
    private MockRepository $mockRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockRepo = new MockRepository('clients');
    }

    public function testBasicExample(): void
    {
        Assert::assertTrue(true, 'Este teste sempre deve passar');
    }
    
    public function testMockRepository(): void
    {
        // Inserir dados mockados
        $clientId = $this->mockRepo->insert([
            'name' => 'João Teste',
            'cpf' => '123.456.789-01'
        ]);
        
        Assert::assertEquals(1, $clientId, 'Primeiro ID deve ser 1');
        
        // Buscar dados
        $client = $this->mockRepo->findById($clientId);
        Assert::assertNotNull($client, 'Cliente deve ser encontrado');
        Assert::assertEquals('João Teste', $client['name'], 'Nome deve estar correto');
    }
    
    public function testCanCreateMultipleClients(): void
    {
        // Criar vários clientes
        $this->mockRepo->insert(['name' => 'Cliente 1']);
        $this->mockRepo->insert(['name' => 'Cliente 2']);
        $this->mockRepo->insert(['name' => 'Cliente 3']);
        
        $allClients = $this->mockRepo->findAll();
        Assert::assertCount(3, $allClients, 'Deve haver 3 clientes');
    }

    public function testCanUpdateClient(): void
    {
        $clientId = $this->mockRepo->insert(['name' => 'Nome Original']);
        
        $updated = $this->mockRepo->update($clientId, ['name' => 'Nome Atualizado']);
        Assert::assertTrue($updated, 'Update deve retornar true');
        
        $client = $this->mockRepo->findById($clientId);
        Assert::assertEquals('Nome Atualizado', $client['name'], 'Nome deve estar atualizado');
    }

    public function testCanDeleteClient(): void
    {
        $clientId = $this->mockRepo->insert(['name' => 'Cliente para deletar']);
        
        $deleted = $this->mockRepo->delete($clientId);
        Assert::assertTrue($deleted, 'Delete deve retornar true');
        
        $client = $this->mockRepo->findById($clientId);
        Assert::assertNull($client, 'Cliente deletado não deve ser encontrado');
    }

    public function testFakeDataGenerator(): void
    {
        $faker = new FakeDataGenerator();
        
        $name = $faker->name();
        $cpf = $faker->cpf();
        $phone = $faker->phone();
        $email = $faker->email();
        
        Assert::assertNotNull($name, 'Faker deve gerar nome');
        Assert::assertStringContains('.', $cpf, 'CPF deve conter pontos');
        Assert::assertStringContains('(', $phone, 'Telefone deve conter parênteses');
        Assert::assertStringContains('@', $email, 'Email deve conter @');
    }

    public function testBaseTestCaseHelpers(): void
    {
        $client = $this->createTestClient(['name' => 'Cliente Helper']);
        Assert::assertEquals('Cliente Helper', $client['name'], 'Nome deve estar correto');
        
        $address = $this->createTestAddress($client['id']);
        Assert::assertEquals($client['id'], $address['client_id'], 'Client ID deve estar correto');
        
        $foundClient = $this->findClientById($client['id']);
        Assert::assertNotNull($foundClient, 'Cliente deve ser encontrado');
        
        $addresses = $this->findAddressesByClientId($client['id']);
        Assert::assertCount(1, $addresses, 'Deve haver 1 endereço');
    }
}
EOF

# Criar teste de Service com Mock
cat > tests/unit/ClientServiceMockTest.php << 'EOF'
<?php

require_once __DIR__ . '/../TestRunner.php';
require_once __DIR__ . '/../Assert.php';
require_once __DIR__ . '/../BaseTestCase.php';
require_once __DIR__ . '/../FakeDataGenerator.php';
require_once __DIR__ . '/../mocks/MockRepository.php';

// Mock da classe Client (simular sua entidade)
class MockClient
{
    private ?int $id = null;
    private string $name;
    private ?string $birthDate;
    private ?string $cpf;
    private ?string $phone;

    public function __construct(string $name, ?string $birthDate = null, ?string $cpf = null, ?string $phone = null)
    {
        $this->name = $name;
        $this->birthDate = $birthDate;
        $this->cpf = $cpf;
        $this->phone = $phone;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getBirthDate(): ?string { return $this->birthDate; }
    public function getCpf(): ?string { return $this->cpf; }
    public function getPhone(): ?string { return $this->phone; }
}

// Mock do ClientService
class MockClientService
{
    private MockRepository $repository;

    public function __construct()
    {
        $this->repository = new MockRepository('clients');
    }

    public function createClient(array $data): bool
    {
        if (empty($data['name'])) {
            throw new InvalidArgumentException('Nome é obrigatório');
        }

        $this->repository->insert($data);
        return true;
    }

    public function getClient(int $id): ?MockClient
    {
        $data = $this->repository->findById($id);
        
        if (!$data) {
            return null;
        }

        $client = new MockClient($data['name'], $data['birth_date'] ?? null, $data['cpf'] ?? null, $data['phone'] ?? null);
        $client->setId($data['id']);
        return $client;
    }

    public function getAllClients(): array
    {
        $allData = $this->repository->findAll();
        $clients = [];

        foreach ($allData as $data) {
            $client = new MockClient($data['name'], $data['birth_date'] ?? null, $data['cpf'] ?? null, $data['phone'] ?? null);
            $client->setId($data['id']);
            $clients[] = $client;
        }

        return $clients;
    }

    public function updateClient(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteClient(int $id): bool
    {
        return $this->repository->delete($id);
    }
}

class ClientServiceMockTest extends BaseTestCase
{
    private MockClientService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new MockClientService();
    }

    public function testCreateClientWithValidData(): void
    {
        $clientData = [
            'name' => 'João Silva',
            'birth_date' => '1990-01-01',
            'cpf' => '123.456.789-01',
            'phone' => '(11) 99999-9999'
        ];

        $result = $this->service->createClient($clientData);
        Assert::assertTrue($result, 'Cliente deve ser criado com sucesso');
    }

    public function testCreateClientWithEmptyNameThrowsException(): void
    {
        Assert::expectException('InvalidArgumentException', function() {
            $this->service->createClient(['name' => '']);
        }, 'Deve lançar exceção para nome vazio');
    }

    public function testGetClientReturnsClientWhenFound(): void
    {
        // Criar cliente primeiro
        $this->service->createClient([
            'name' => 'João Teste',
            'cpf' => '123.456.789-01'
        ]);

        $client = $this->service->getClient(1);
        
        Assert::assertInstanceOf('MockClient', $client, 'Deve retornar instância de MockClient');
        Assert::assertEquals('João Teste', $client->getName(), 'Nome deve estar correto');
    }

    public function testGetClientReturnsNullWhenNotFound(): void
    {
        $client = $this->service->getClient(999);
        Assert::assertNull($client, 'Deve retornar null para cliente inexistente');
    }

    public function testGetAllClients(): void
    {
        $this->service->createClient(['name' => 'Cliente 1']);
        $this->service->createClient(['name' => 'Cliente 2']);
        $this->service->createClient(['name' => 'Cliente 3']);

        $clients = $this->service->getAllClients();
        Assert::assertCount(3, $clients, 'Deve retornar 3 clientes');
        
        foreach ($clients as $client) {
            Assert::assertInstanceOf('MockClient', $client, 'Cada item deve ser MockClient');
        }
    }
}
EOF

# Criar script para executar testes
echo "Criando script de execução..."
cat > tests/run_tests.php << 'EOF'
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
EOF

chmod +x tests/run_tests.php

# Criar Makefile
echo "Criando Makefile..."
cat > Makefile << 'EOF'
.PHONY: test test-unit test-watch test-mock

test:
	php tests/run_tests.php

test-unit:
	php tests/run_tests.php

test-mock:
	php tests/run_tests.php

test-watch:
	watch -n 2 php tests/run_tests.php

test-clean:
	@echo "Limpando dados em memória (nada a fazer - já é mockado!)"
EOF

echo "Criando documentação..."
cat > tests/README.md << 'EOF'
# Sistema de Testes com Mocks (Sem Banco)

Este sistema de testes é **100% mockado** e **não depende de banco de dados**.

## Características

- **Dados em memória**: Nenhuma conexão com MySQL
- **Mocks nativos**: Repositórios falsos em PHP puro
- **Dados falsos**: FakeDataGenerator para dados realistas
- **Testes rápidos**: Execução instantânea
- **Isolamento total**: Cada teste roda independente

## Como Usar

```bash
# Executar todos os testes
php tests/run_tests.php

# Ou via make
make test
```

## Estrutura

```
tests/
├── TestRunner.php          # Framework de testes
├── Assert.php              # Assertions
├── BaseTestCase.php        # Classe base (sem banco)
├── FakeDataGenerator.php   # Dados falsos
├── mocks/
│   └── MockRepository.php  # Repositório em memória
├── unit/
│   ├── ExampleMockTest.php
│   └── ClientServiceMockTest.php
└── run_tests.php           # Script principal
```

## Exemplo de Teste

```php
class MeuTest extends BaseTestCase
{
    public function testAlgumaCoisa(): void
    {
        // Criar dados mockados
        $client = $this->createTestClient(['name' => 'Teste']);
        
        // Testar
        Assert::assertEquals('Teste', $client['name']);
    }
}
```

## MockRepository

Use para simular operações de banco:

```php
$mockRepo = new MockRepository('clients');
$id = $mockRepo->insert(['name' => 'João']);
$client = $mockRepo->findById($id);
```

Todos os dados ficam **apenas em memória** durante o teste!
EOF

echo ""
echo "Sistema de Testes com Mocks configurado!"
echo ""
echo "PRINCIPAIS VANTAGENS:"
echo "   Zero dependência de banco de dados"
echo "   Testes super rápidos (apenas memória)"
echo "   Dados 100% controlados e mockados"
echo "   Execução isolada e limpa"
echo ""
echo "Como usar:"
echo "   php tests/run_tests"