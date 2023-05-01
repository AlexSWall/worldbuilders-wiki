# Include environment if it exists, and ignore if it doesn't.
-include .env

# Directory Structure
ROOT_DIR=$(shell pwd)
LOGS_DIR=${ROOT_DIR}/logs
MYSQL_DUMPS_DIR=${ROOT_DIR}/data/db/dumps
SETUP_DIR=${ROOT_DIR}/setup

help:
	@echo "Usage:"
	@echo '$$ make <command>'
	@echo ""
	@echo "Commands:"
	@echo ""
	@echo "  help                Shows this help."
	@echo ""
	@echo "  setup               Set up the repository for a wiki instance from instance data contained in a root repo directory named 'setup'."
	@echo ""
	@echo '  start               Starts the docker-compose containers as a daemon. (Just aliases `docker-compose up -d`.)'
	@echo '  stop                Stops the running docker-compose daemon. (Just aliases `docker-compose down`.)'
	@echo ""
	@echo "  db-dump             Create a backup of all databases to './data/db/dumps/db.sql'."
	@echo "  db-restore          Restore all databases from a backup stored at './data/db/dumps/db.sql'."
	@echo ""
	@echo "  test                Run the backend tests."
	@echo ""
	@echo "  clean               Delete files and directories generated when running the wiki, such as logs and libraries."
	@echo "  distclean           Run clean and additionally delete the instance's configuration files."

${LOGS_DIR}:
	mkdir -p $@

${MYSQL_DUMPS_DIR}:
	mkdir -p $@

.PHONY: setup
setup: | ${LOGS_DIR} ${MYSQL_DUMPS_DIR}
	@touch ${LOGS_DIR}/{app,tests}.log
	@chmod 666 ${LOGS_DIR}/{app,tests}.log
	@touch ${ROOT_DIR}/config/etc/nginx/default.conf
	@cp ${SETUP_DIR}/*.config.php ${ROOT_DIR}/config/backend/
	@cp ${SETUP_DIR}/.env ${ROOT_DIR}
	@$(MAKE) setup-database

.PHONY: setup-database
setup-database:
	@docker-compose up -d mysqldb
	@echo "Sleeping for 15 seconds to allow MySQL container to spin up..."
	@sleep 15
	@docker exec -i mysql mysql -u"root" -p"$(MYSQL_ROOT_PASSWORD)" website < ${SETUP_DIR}/db.sql
	@docker-compose down

.PHONY: db-dump
db-dump:
	@mkdir -p $(MYSQL_DUMPS_DIR)
	@docker exec $(shell docker-compose ps -q mysqldb) mysqldump --all-databases -u"$(MYSQL_ROOT_USER)" -p"$(MYSQL_ROOT_PASSWORD)" > $(MYSQL_DUMPS_DIR)/db.sql 2>/dev/null
	@chown -Rf $(USER):$(shell id -g -n $(USER)) $(MYSQL_DUMPS_DIR)
	@echo "Database dumped to '$(MYSQL_DUMPS_DIR)/db.sql'."

.PHONY: db-restore
db-restore:
	@echo "Restoring database from '$(MYSQL_DUMPS_DIR)/db.sql'."
	@docker exec -i $(shell docker-compose ps -q mysqldb) mysql -u"$(MYSQL_ROOT_USER)" -p"$(MYSQL_ROOT_PASSWORD)" website < $(MYSQL_DUMPS_DIR)/db.sql

.PHONY: test
test:
	@docker-compose exec -T php ./backend/vendor/bin/phpunit --colors=always ./backend/tests

.PHONY: clean
clean:
	@rm -rf ${LOGS_DIR}
	@rm -rf web/backend/vendor
	@rm -rf web/backend/composer.lock
	@rm -rf web/frontend/node_modules

.PHONY: distclean
distclean: clean
	@rm -rf data
	@rm -rf ${ROOT_DIR}/config/backend/*.config.php
	@rm -rf ${ROOT_DIR}/.env

