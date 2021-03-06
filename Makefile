# Misc
.ONESHELL:
.DEFAULT_GOAL = help

# Executables
PHP = php
COMPOSER = composer
DOCKER = docker
DOCKER_COMPOSE = docker-compose
SYMFONY = symfony

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-18s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m\n/'

## ——————————————————————————————— BACK ———————————————————————————————

## —— Composer 🧙️ —————————————————————————————————————————————————————

install: composer.lock ## Install vendors according to the current composer.lock file
	@$(COMPOSER) install --no-progress --prefer-dist --optimize-autoloader

## —— Symfony 🎵️ —————————————————————————————————————————————————————

serve: ## Start server
	@$(SYMFONY) serve -d

server-stop: ## Stop server
	@$(SYMFONY) server:stop

migrate: ## Update datatable structure
	@$(SYMFONY) console doctrine:migrations:migrate --no-interaction

reset-datatable: ## Delete and recreate datatable
	@$(SYMFONY) console doctrine:database:drop --force
	@$(SYMFONY) console doctrine:database:create
	@$(SYMFONY) console doctrine:migrations:migrate --no-interaction

fixtures: ## Start fixtures
	@$(SYMFONY) console doctrine:fixtures:load --no-interaction

keypair: ## Create keypair for SecurityBundle
	@$(SYMFONY) console lexik:jwt:generate-keypair

## —— RabbitMQ 🐇️ —————————————————————————————————————————————————————

consume: ## Consume all messages
	@$(SYMFONY) console messenger:consume -vv

open-rabbitmq-admin: ## Open admin website
	@$(SYMFONY) open:local:rabbitmq

## ——————————————————————————————— OTHER ——————————————————————————————

## —— Docker 🐳 ———————————————————————————————————————————————————————

up: ## Start the docker hub
	$(DOCKER_COMPOSE) up --detach

up-no-detach: ## Start the docker hub
	$(DOCKER_COMPOSE) up

build: ## Builds the images
	@$(DOCKER_COMPOSE) build --pull --no-cache

down: ## Stop the docker hub
	@$(DOCKER_COMPOSE) down --remove-orphans

sh: ## Log to the docker container
	@$(DOCKER_COMPOSE) exec php sh

logs: ## Show live logs
	@$(DOCKER_COMPOSE) logs --tail=0 --follow

## —— Project 🐝 ——————————————————————————————————————————————————————

init: install up serve reset-datatable fixtures keypair server-stop down  ## Initialize project, need to run after git clone
	@echo "\n🎉 Done ! 🎉\n"
	@echo "Execute \"make start\" for launch all the environnement ! 🐳\n"
	@echo "Execute \"make stop\" when you are done in order to close the environment.\n"
	@echo "And \"make help\" for list all available commands."

start: up serve open-browser open-webhook-site open-rabbitmq-admin ## Start Docker

stop: server-stop down ## Stop Docker

open-browser: ## Open website into browser
	@sleep 3
	@xdg-open 'https://127.0.0.1:8000/docs'

open-webhook-site: ## Open webhook.site into browser
	@xdg-open 'https://webhook.site/#!/1e943e77-c1db-4ae7-8969-2fc51e5eee5c'
