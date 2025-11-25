# Sobre o Projeto

Este projeto consiste em um sistema de CRUD completo para pessoas e empresas, implementado em Laravel. O relacionamento entre pessoas e empresas é do tipo N para N, ou seja, uma pessoa pode estar vinculada a várias empresas e uma empresa pode ter várias pessoas associadas. O sistema foi desenvolvido seguindo os princípios de Clean Code, SOLID e PSR-4, garantindo organização, extensibilidade e manutenibilidade.

## Estrutura das Pastas

A estrutura do projeto segue o padrão Laravel, com separação clara entre controllers, services, models, events e testes:

- `app/Http/Controllers/`: Controllers finos, responsáveis apenas por orquestrar requisições, validação e autenticação.
- `app/Services/`: Centraliza toda a lógica de negócio, respeitando o Princípio da Responsabilidade Única.
- `app/Models/`: Modelos das entidades principais, incluindo Pessoa e Empresa.
- `app/Events/`: Eventos do sistema, organizados por módulo.
- `tests/`: Testes automatizados com PHPUnit, cobrindo controllers, services e casos de uso.

## Boas Práticas e Padrões

- Classes em PascalCase e métodos com comentários PHPDoc.
- Controllers devem delegar regras de negócio para services.
- Testes seguem a convenção `testNomeDoTeste` e utilizam mocks para dependências externas.
- Testes devem cobrir controllers, services e casos de uso.
- Não deve ter função de tearDown, pois o PHPUnit já lida com isso.
- Utilize setUp para preparar o ambiente de teste, se necessário.
- Deve utilizar factory para criar dados de teste.
- Testes em snake_case e com 
- Filtros em requisições GET seguem o padrão `filtros[coluna] = valor`.
- Eventos registrados conforme padrão Laravel, utilizando enums para tipos de log.

# Requisitos

- PHP >= 8.2
- Composer
- Laravel 12.0

Para executar os testes automatizados, utilize o Makefile:

```bash
make test
```