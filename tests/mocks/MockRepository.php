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
