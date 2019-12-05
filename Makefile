QA        	  = docker run --rm -v `pwd`:/project -w /project guillaumedelre/qat
COMPOSER  	  = cd $(BASE_PATH) && composer
EXEC_PHP	  = cd $(BASE_PATH) && php
SYMFONY  	  = bin/console
ARTIFACT_DIR  = var/build/qa
SUCCESS       = \033[0;32m
ERROR         = \033[0;31m
COMMENT	      = \033[0;36m
SECTION	      = \033[0;33m
END	          = \033[0m

artifacts:
	mkdir -p $(ARTIFACT_DIR)

docker-build-image:
	docker build -t guillaumedelre/qat docker

# rules based on files
composer.lock: composer.json
	@$(DCEXAPP) '$(COMPOSER) update --lock --no-scripts --no-interaction'

vendor: composer.lock
	@$(DCEXAPP) '$(COMPOSER) install'

##
## Viens voir le docteur ... NaN nez pas peur  :)
## -----------------------------------------------
##

doctor: ## Generate Mezzo health check
doctor: db-check lint security phploc pdepend phpmd phpcpd phpdcd phpstan phpmetrics

##
## Database tools
## --------------
##

db-check: ## Check the mapping of your entitites
db-check: artifacts
	@$(SYMFONY) doctrine:mapping:info > $(ARTIFACT_DIR)/db-check-report.txt \
	&& echo "$(SECTION)[LINT]$(END) \\nChecking the mapping ... $(SUCCESS)OK$(END)" \
	|| echo "$(SECTION)[LINT]$(END) \\nChecking the mapping ... $(ERROR)KO$(END)" \
	&& echo "Please check $(COMMENT)$(ARTIFACT_DIR)/db-check-report.txt.$(END)\\n"

db-purge: ## drop de schema of the database
db-purge:
	@$(SYMFONY) doctrine:schema:drop --force --full-database

##
## Quality assurance
## -----------------
##

lint: ## Lints yaml files
lint: artifacts
	@$(SYMFONY) lint:yaml config -vvv --parse-tags --format=json > $(ARTIFACT_DIR)/lint-yaml-report.json \
	&& echo "$(SECTION)[LINT]$(END) \\nLinting yaml files ... $(SUCCESS)OK$(END)" \
	|| echo "$(SECTION)[LINT]$(END) \\nLinting yaml files ... $(ERROR)KO$(END)" \
	&& echo "Please check $(COMMENT)$(ARTIFACT_DIR)/lint-yaml-report.json.$(END)\\n"

security: ## Check security of your dependencies (https://security.sensiolabs.org/)
security:
	@$(QA) security-checker security:check composer.lock --format=json > $(ARTIFACT_DIR)/security-check-report.json \
	&& echo "$(SECTION)[SECURITY CHECK]$(END) \\nChecking dependencies ... $(SUCCESS)OK$(END)" \
	|| echo "$(SECTION)[SECURITY CHECK]$(END) \\nChecking dependencies ... $(ERROR)KO$(END)" \
	&& echo "Please check $(COMMENT)$(ARTIFACT_DIR)/security-check-report.json.$(END)\\n"

phploc: ## PHPLoc (https://github.com/sebastianbergmann/phploc)
phploc: artifacts
	@$(QA) phploc src > $(ARTIFACT_DIR)/phploc-report.txt \
	&& echo "$(SECTION)[PHP LOC]$(END) \\nPerforming static code analysis ... $(SUCCESS)OK$(END)" \
	|| echo "$(SECTION)[PHP LOC]$(END) \\nPerforming static code analysis ... $(ERROR)KO$(END)" \
	&& echo "Please check $(COMMENT)$(ARTIFACT_DIR)/phploc-report.txt.$(END)\\n"

pdepend: ## PHP_Depend (https://pdepend.org)
pdepend: artifacts
	@$(QA) pdepend \
	--quiet \
	--summary-xml=$(ARTIFACT_DIR)/pdepend-report.xml \
	--jdepend-chart=$(ARTIFACT_DIR)/pdepend-jdepend.svg \
	--overview-pyramid=$(ARTIFACT_DIR)/pdepend-pyramid.svg \
	src/ \
	&& echo "$(SECTION)[PHP DEPEND]$(END) \\nPerforming static code analysis ... $(SUCCESS)OK$(END)" \
	|| echo "$(SECTION)[PHP DEPEND]$(END) \\nPerforming static code analysis ... $(ERROR)KO$(END)" \
	&& echo "Please check $(COMMENT)$(ARTIFACT_DIR)/pdepend-report.xml.$(END)\\n"

phpmd: ## PHP Mess Detector (https://phpmd.org)
phpmd: artifacts
	@$(QA) phpmd src json /usr/local/src/.phpmd.xml > $(ARTIFACT_DIR)/phpmd-report.json \
	|| echo "Please check $(COMMENT)$(ARTIFACT_DIR)/phpmd-report.json.$(END)\\n"

phpcpd: ## PHP Copy/Paste Detector (https://github.com/sebastianbergmann/phpcpd)
phpcpd: artifacts
	@$(QA) phpcpd src > $(ARTIFACT_DIR)/phpcpd-report.txt \
	|| echo "Please check $(COMMENT)$(ARTIFACT_DIR)/phpcpd-report.txt.$(END)\\n"

phpdcd: ## PHP Dead Code Detector (https://github.com/sebastianbergmann/phpdcd)
phpdcd: artifacts
	@$(QA) phpdcd src > $(ARTIFACT_DIR)/phpdcd-report.txt \
	|| echo "Please check $(COMMENT)$(ARTIFACT_DIR)/phpdcd-report.txt.$(END)\\n"

phpstan: ## PHP Static Analysis Tool (https://github.com/phpstan/phpstan)
phpstan: artifacts
	@$(QA) phpstan analyse -c docker/phpstan.neon -l max --error-format prettyJson src > $(ARTIFACT_DIR)/phpstan-report.json \
	|| echo "\n Please check $(COMMENT)$(ARTIFACT_DIR)/phpstan-report.json.$(END)\\n"

phpmetrics: ## PhpMetrics (http://www.phpmetrics.org)
phpmetrics: artifacts
	@$(QA) phpmetrics --quiet --report-html=$(ARTIFACT_DIR)/phpmetrics src \
	&& echo "$(SECTION)[PHP METRICS]$(END) \\nPerforming static code analysis ... $(SUCCESS)OK$(END)" \
	|| echo "$(SECTION)[PHP METRICS]$(END) \\nPerforming static code analysis ... $(ERROR)KO$(END)" \
	&& echo "Please check $(COMMENT)$(ARTIFACT_DIR)/phpmetrics/index.html.$(END)\\n"

phpcsfix: ## PHP Coding Standards Fixer (https://cs.symfony.com/)
phpcsfix: artifacts
	@$(QA) php-cs-fixer fix --config=docker/.php_cs.dist --dry-run --using-cache=no --verbose --diff --format=json  > $(ARTIFACT_DIR)/php-cs-fixer-report.json \
	|| echo "Please check $(COMMENT)$(ARTIFACT_DIR)/php-cs-fixer-report.json.$(END)\\n"

phpcsfix-apply: ## Apply php-cs-fixer fixes
phpcsfix-apply:
	@$(QA) php-cs-fixer fix --config=docker/.php_cs.dist --using-cache=no --verbose

##
## Tests
## -----
##

test: ## Run unit tests
test: phpunit

phpunit: ## Run unit tests
phpunit: vendor
	$(QA) simple-phpunit --stop-on-failure --stop-on-error

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
