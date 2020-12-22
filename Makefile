# Makefile for Docker Nginx PHP Composer MySQL

include .env

.env:
	touch .env

ROOT_DIR=$(shell pwd)
LOGS_DIR=${ROOT_DIR}/logs
MYSQL_DUMPS_DIR=${ROOT_DIR}/data/db/dumps
SETUP_DIR=${ROOT_DIR}/setup

help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"
	@echo "  clean               Clean directories"
	@echo "  docker-start        Create and start containers"
	@echo "  docker-stop         Stop and clear all services"
	@echo "  gen-certs           Generate SSL certificates"
	@echo "  logs                Follow log output"
	@echo "  mysql-dump          Create backup of all databases"
	@echo "  mysql-restore       Restore backup of all databases"
	@echo "  test                Test application"

${LOGS_DIR}:
	mkdir -p $@

${MYSQL_DUMPS_DIR}:
	mkdir -p $@

setup: | ${LOGS_DIR} ${MYSQL_DUMPS_DIR}
	@touch ${LOGS_DIR}/{app,tests}.log
	@chmod 666 ${LOGS_DIR}/{app,tests}.log
	@touch ${ROOT_DIR}/config/etc/nginx/default.conf
	@cp ${SETUP_DIR}/*.config.php ${ROOT_DIR}/config/backend/
	@cp ${SETUP_DIR}/.env ${ROOT_DIR}
	@$(MAKE) setup-database

setup-database:
	@docker-compose up -d mysqldb
	@echo "Sleeping to allow mysql container to set up."
	@sleep 10
	@docker exec -i mysql mysql -u"root" -p"$(MYSQL_ROOT_PASSWORD)" website < ${SETUP_DIR}/db.sql
	@docker-compose down

clean:
	@rm -rf ${LOGS_DIR}
	@rm -rf web/backend/vendor
	@rm -rf web/backend/composer.lock
	@rm -rf web/frontend/node_modules

shiny: clean
	@rm -rf data
	@rm -rf ${ROOT_DIR}/config/backend/*.config.php
	@rm -rf ${ROOT_DIR}/.env

docker-start:
	docker-compose up -d

docker-stop:
	@docker-compose down -v

gen-certs:
	@docker run --rm -v $(shell pwd)/etc/ssl:/certificates -e "SERVER=$(NGINX_HOST)" jacoelho/generate-certificate

mysql-dump:
	@mkdir -p $(MYSQL_DUMPS_DIR)
	@docker exec $(shell docker-compose ps -q mysqldb) mysqldump --all-databases -u"$(MYSQL_ROOT_USER)" -p"$(MYSQL_ROOT_PASSWORD)" > $(MYSQL_DUMPS_DIR)/db.sql 2>/dev/null
	@make resetOwner

mysql-restore:
	@docker exec -i $(shell docker-compose ps -q mysqldb) mysql -u"$(MYSQL_ROOT_USER)" -p"$(MYSQL_ROOT_PASSWORD)" website < $(MYSQL_DUMPS_DIR)/db.sql

test:
	@docker-compose exec -T php ./backend/vendor/bin/phpunit --colors=always ./backend/tests

resetOwner:
	@$(shell chown -Rf $(SUDO_USER):$(shell id -g -n $(SUDO_USER)) $(MYSQL_DUMPS_DIR) "$(shell pwd)/etc/ssl" "$(shell pwd)/web/app" 2> /dev/null)

.PHONY: setup clean shiny test
