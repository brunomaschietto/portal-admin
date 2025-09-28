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
