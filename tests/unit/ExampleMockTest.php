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
