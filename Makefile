# Variáveis de cor ANSI para facilitar manutenção e entendimento
# COLOR_TITLE: ciano claro (bold cyan) para títulos
COLOR_TITLE = \033[1;36m
# COLOR_CMD: verde claro (bold green) para comandos
COLOR_CMD   = \033[1;32m
# COLOR_RESET: reset de cor para texto padrão
COLOR_RESET = \033[0m

# Variável que armazena os argumentos passados após o comando principal
ARGUMENTOS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))

# ARG1: primeiro argumento passado após o comando principal
ARG1 := $(word 1, $(ARGUMENTOS))

# Variável para reutilizar o comando de execução do artisan via docker
ARTISAN = docker exec -t bry-app php artisan
# Variável para executar o Composer dentro do container
COMPOSER = docker exec -t bry-app composer
# Variavel para executar o .vendor dentro do container
VENDOR = docker exec -t bry-app ./vendor/bin/

.PHONY: test test-coverage testsuite test-filter testsuite-coverage coverage-clean help migrate migrate-rollback migrate-seed seed composer-install composer-dump scribe

## Executa todos os testes
test:
	$(ARTISAN) test

## Executa todos os testes com cobertura e gera relatório HTML em storage/coverage
test-coverage:
	$(ARTISAN) test --coverage --coverage-html=storage/coverage

## Executa os testes de uma suite específica. Exemplo: make testsuite scanner
testsuite:
	$(ARTISAN) test --testsuite $(ARG1)

## Executa um teste filtrando pelo nome. Exemplo: make test-filter AtualizarComentarioTest
test-filter:
	$(ARTISAN) test --filter="$(ARG1)"

## Executa os testes de uma suite específica e gera o relatório de cobertura. Exemplo: make testsuite-coverage scanner
testsuite-coverage:
	$(ARTISAN) test --testsuite $(ARG1) --coverage --coverage-html=storage/coverage

## Limpa os arquivos de cobertura gerados
coverage-clean:
	rm -rf storage/coverage

## Executa as migrations do banco de dados
migrate:
	$(ARTISAN) migrate

## Executa as migrations do banco de dados
migrate-fresh:
	$(ARTISAN) migrate:fresh

## Executa um rollback das migrations do banco de dados
migrate-rollback:
	$(ARTISAN) migrate:rollback

## Executa migrations e popula o banco de dados com os seeders
migrate-seed:
	$(ARTISAN) migrate --seed

seed:
	@if [ -z "$(ARG1)" ]; then \
		$(ARTISAN) db:seed; \
	else \
		$(ARTISAN) db:seed --class="$(ARG1)"; \
	fi

## Instala as dependências PHP via Composer
composer-install:
	$(COMPOSER) install --no-interaction --prefer-dist --optimize-autoloader

## Atualiza o autoload do Composer (otimizado)
composer-dump:
	$(COMPOSER) dump-autoload -o

## Sobe os containers do Docker
up:
	docker-compose up -d

## Desliga os containers do Docker
down:
	docker-compose down

## Exibe ajuda dos comandos disponíveis com cores
help:
	@echo "$(COLOR_TITLE)=== Comandos disponíveis: =====================================$(COLOR_RESET)"
	@echo ""
	@echo "$(COLOR_TITLE)  Testes:$(COLOR_RESET)"
	@echo "  $(COLOR_CMD)make test$(COLOR_RESET)                         - Executa todos os testes"
	@echo "  $(COLOR_CMD)make test-coverage$(COLOR_RESET)                - Executa todos os testes com cobertura (HTML em storage/coverage)"
	@echo "  $(COLOR_CMD)make testsuite <suite>$(COLOR_RESET)            - Executa os testes da suite informada"
	@echo "  $(COLOR_CMD)make test-filter <nomeDoTeste>$(COLOR_RESET)    - Executa o teste filtrando pelo nome"
	@echo "  $(COLOR_CMD)make testsuite-coverage <suite>$(COLOR_RESET)   - Executa os testes da suite e gera cobertura"
	@echo "  $(COLOR_CMD)make coverage-clean$(COLOR_RESET)               - Limpa os arquivos de cobertura"
	@echo ""
	@echo "$(COLOR_TITLE)  Banco de Dados:$(COLOR_RESET)"
	@echo "  $(COLOR_CMD)make migrate$(COLOR_RESET)                      - Executa as migrations"
	@echo "  $(COLOR_CMD)make migrate-fresh$(COLOR_RESET)                - Recria o banco de dados e executa todas as migrations"
	@echo "  $(COLOR_CMD)make migrate-rollback$(COLOR_RESET)             - Executa um rollback das migrations"
	@echo "  $(COLOR_CMD)make migrate-seed$(COLOR_RESET)                 - Executa migrations e seeders"
	@echo "  $(COLOR_CMD)make seed <nomeDaSeed>$(COLOR_RESET)            - Executa a seed informada"
	@echo ""
	@echo "$(COLOR_TITLE)  Desenvolvimento:$(COLOR_RESET)"
	@echo "  $(COLOR_CMD)make up$(COLOR_RESET)                            - Sobe os containers Docker"
	@echo "  $(COLOR_CMD)make down$(COLOR_RESET)                          - Desliga os containers Docker"
	@echo "  $(COLOR_CMD)make composer-install$(COLOR_RESET)             - Instala dependências PHP via Composer"
	@echo "  $(COLOR_CMD)make composer-dump$(COLOR_RESET)                 - Executa o Laravel Pint (ex: --test, -v, --path=)"
	@echo ""
	@echo "$(COLOR_TITLE)  Ajuda:$(COLOR_RESET)"
	@echo "  $(COLOR_CMD)make help$(COLOR_RESET)                         - Exibe esta ajuda"
	@echo ""
	@echo "$(COLOR_TITLE)=================================================================$(COLOR_RESET)"
# Evita erro de "No rule to make target" ao passar argumentos
%:
	@:
