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
