# Sistema de Gerenciamento de Clientes

O Sistema de Gerenciamento de Clientes é uma aplicação web desenvolvida em **PHP puro (sem frameworks)** que permite o cadastro, edição e gerenciamento de clientes e seus endereços.  
O sistema inclui autenticação de usuários, interface responsiva e testes automatizados com mocks.

---

## 🔍 Índice
- [Funcionalidades do Projeto](#-funcionalidades-do-projeto)
- [Layout](#-layout)
- [Como Executar este Projeto](#-como-executar-este-projeto)
- [Tecnologias Utilizadas](#%EF%B8%8F-tecnologias-utilizadas)
- [Testes](#-testes)
- [Autores](#-autores)

---

## 💻 Funcionalidades do Projeto
- Sistema de login e registro de usuários com autenticação por sessão.
- CRUD completo de clientes (criar, listar, editar, excluir).
- Gerenciamento de múltiplos endereços por cliente com adição/remoção dinâmica.
- Interface responsiva desenvolvida com CSS puro.
- Validação de dados no frontend e backend.
- Sistema de testes automatizados com mocks (sem dependência de banco).
- Arquitetura limpa seguindo princípios de separação de responsabilidades.

---

## 🖼 Layout
Para uma prévia visual, confira abaixo algumas das principais telas do sistema:

- **Tela Login**: Interface moderna de autenticação com campos de usuário e senha.  
- **Tela de Registro**: Cadastro de novos usuários com validação de dados.  
- **Lista de Clientes**: Tabela responsiva com listagem completa dos clientes cadastrados.  
- **Cadastro de Cliente**: Formulário para criar novos clientes com múltiplos endereços.  
- **Edição de Cliente**: Interface para editar dados pessoais e gerenciar endereços do cliente.  

Cada tela foi desenvolvida com foco na usabilidade e responsividade, garantindo uma experiência consistente em dispositivos desktop e mobile.

---

## 🕹 Como Executar este Projeto

### 1. Clone este repositório
```bash
git clone https://github.com/brunomaschietto/portal-admin.git
cd portal-admin
```

### 2. Instale as dependências
```bash
docker-compose up -d --build
```

### 3. Execute o Sistema
```bash
php -S localhost:8081
```
Acesse [http://localhost:8081](http://localhost:8081) no navegador.

### 4. Configure os Testes
```bash
chmod +x setup_custom_tests.sh
./setup_custom_tests.sh

php tests/run_tests.php
```

> **Nota:** Os testes utilizam mocks e não dependem de banco de dados.

---

## ⚙️ Tecnologias Utilizadas
- **PHP 8.0+** — Linguagem de programação principal do backend.  
- **MySQL 8.0+** — Sistema de gerenciamento de banco de dados.  
- **PDO** — Interface de acesso ao banco de dados.  
- **Composer** — Gerenciador de dependências PHP.  
- **HTML5 / CSS3 / JavaScript** — Frontend responsivo.  

---

## 🧪 Testes

### Executando os Testes
```bash
php tests/run_tests.php
# ou via Makefile
make test
```

### Características dos Testes
- 🚀 **100% Mockados**: Não dependem de banco de dados  
- ⚡ **Execução Rápida**: Testes rodam em milissegundos  
- 🔒 **Isolados**: Cada teste executa independentemente  
- 📝 **Cobertura Completa**: Testa entidades, repositórios e serviços  

### Exemplo de Saída
```
🧪 Executando Testes Automatizados (Com Mocks)...
==================================================

📋 Executando: ClientServiceMockTest
  ✅ testCreateClientWithValidData
  ✅ testCreateClientWithEmptyNameThrowsException
  ✅ testGetClientReturnsClientWhenFound

=====================================
📊 Resumo dos Testes
=====================================
Total: 3
✅ Passou: 3
❌ Falhou: 0
⏱️ Tempo: 0.05s

🎉 Todos os testes passaram! (100% mockados)
```

---

## 👩🏻‍💻 Autores
Este projeto foi desenvolvido por **Bruno Maschietto**.

[LinkedIn](https://www.linkedin.com/in/bruno-maschietto/)
