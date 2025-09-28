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
