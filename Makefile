.DEFAULT_GOAL = help
SF=symfony
CONSOLE=$(SF) console
COMPOSER = composer
NPM = npm
DOCKER = docker
DC = $(DOCKER) compose

##
## —— Utils ⚙️ ————————————————————————————————————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

cc:			## Clear cache
	$(CONSOLE) ca:cl -e $(or $(ENV), 'dev')

install: 	## Install project
install: config start db-init db-reload

start:		## Start project
start: dependencies docker-start sf-start dev-assets

stop:		## Stop project
stop: docker-stop sf-stop
	$(SF) server:stop
	$(DC) stop

restart:	## Restart project
restart: stop start

sf-start: 	## Start symfony server
sf-start:
	$(SF) server:start -d

sf-stop: 	## Stop symfony server
sf-stop:
	$(SF) server:stop

dev-assets:		## Build assets
dev-assets:
	$(NPM) run dev

##
## —— Docker 🐳 ————————————————————————————————————————————————————————————————
dependencies:	## Install dependencies
dependencies: vendors npm

docker-start:		## Start docker container
	@$(DC) up -d --remove-orphans

docker-stop:		## Stop docker container
	@$(DC) stop

##
## —— Dependencies 🧱 ————————————————————————————————————————————————————————————————
vendors:	## Install php dependencies
	$(COMPOSER) install

npm:		## Install front dependencies
	$(NPM) install

##
## —— Database 📊 ————————————————————————————————————————————————————————————————
db-init: 	## Init project's database
	$(CONSOLE) d:d:drop -n --force --if-exists
	$(CONSOLE) d:d:create -q

db-diff:   	## Creates doctrine migration
	$(CONSOLE) doc:mi:diff

db-migrate:	## Runs doctrine migration
	$(CONSOLE) d:m:migrate -n

db-fixtures:	## Load fixtures
	$(CONSOLE) doctrine:fixtures:load -n --append

db-reload:	## Reloads project's data
ifeq (, $(shell which symfony))
db-reload: CONSOLE=php bin/console
endif
db-reload: db-init db-migrate

##
## —— Configuration 📋 ————————————————————————————————————————————————————————————————
config:		## Init project configuration
config: env.local compose.override.yaml

compose.override.yaml: compose.override.yaml.dist
	@echo "🖍️ Copying compose override distant file"
	@cp compose.override.yaml.dist compose.override.yaml

env.local: .env.local.dist
	@echo "🖍️ Copying env file"
	@cp .env.local.dist .env.local