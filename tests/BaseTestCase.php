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
