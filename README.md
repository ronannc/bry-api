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

## Processamento de Duplicados

Para identificar e processar pessoas duplicadas, utilize o comando Artisan abaixo:

```sh
php artisan person:duplicates
```

Este comando executa a lógica de detecção e tratamento de duplicidades, consolidando registros conforme regras de negócio.

## Seeder de Pessoas

Para popular o banco de dados com dados de exemplo de pessoas, execute:

```sh
php artisan db:seed --class=PersonSeeder
```

## Estratégia para Tratamento de Duplicados

A identificação de duplicados é realizada por meio de uma Job, que executa em fila para cada pessoa e verifica se há registros similares. São considerados duplicados:

- Pessoas com CPF igual.
- Pessoas com nomes foneticamente semelhantes, utilizando duas abordagens:
  - Algoritmo dmetaphone para comparação fonética dos nomes.
  - Similaridade de nomes com limiar 3 (threshold), que pode ser ajustado conforme testes em outras bases de dados.

Após o processamento, os dados são salvos em uma tabela de cache dos duplicados (`person_duplicates_cache`). Para consulta rápida, a API fornece a rota:

```
GET /api/identidades/duplicadas
```
