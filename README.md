# Bry API

API de CRUD completo para Pessoas e Empresas.

## Descrição
Esta API permite o gerenciamento de pessoas e empresas, onde:
- Cada empresa pode ter várias pessoas associadas, categorizadas como FUNCIONÁRIO ou CLIENTE.
- Cada pessoa pode estar vinculada a várias empresas.

## Funcionalidades
- CRUD de Pessoas
- CRUD de Empresas
- Associação entre pessoas e empresas com categorização

## Como executar o projeto

### Pré-requisitos
- Docker instalado
- Make instalado

### Passos para execução
1. Copie o arquivo de variáveis de ambiente:
   ```sh
   cp .env.example .env
   ```
2. Suba os containers Docker:
   ```sh
   make up
   ```
3. Instale as dependências do Composer:
   ```sh
   make composer-install
   ```
4. Execute as migrações do banco de dados:
   ```sh
   make migrate
   ```

A API estará disponível conforme configuração do seu ambiente Docker.

## Testes
Para rodar os testes:
```sh
make test
```
