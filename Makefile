.DEFAULT_GOAL := help

WORKDIR_MOBILE := gerayehealthcare-mobile-app
PHP := php
ARTISAN := $(PHP) artisan
FLUTTER := flutter
DART_DEFINE := --dart-define=APP_ENV=development

.PHONY: help
help:
	@echo "Available targets:"
	@echo "  make setup            # Install backend & mobile dependencies"
	@echo "  make backend          # Start Laravel API (http://127.0.0.1:8000)"
	@echo "  make backend-queue    # Run Laravel queue worker"
	@echo "  make backend-migrate  # Run migrations & seed demo data"
	@echo "  make mobile-setup     # Fetch Flutter packages & generate code"
	@echo "  make mobile-run       # Launch Flutter app in development env"
	@echo "  make mobile-analyze   # Static analysis"
	@echo "  make mobile-test      # Widget/unit tests"

.PHONY: setup
setup: composer-install npm-install mobile-setup

.PHONY: composer-install
composer-install:
	composer install

.PHONY: npm-install
npm-install:
	npm install

.PHONY: backend
backend:
	$(ARTISAN) serve --host=127.0.0.1 --port=8000

.PHONY: backend-queue
backend-queue:
	$(ARTISAN) queue:work

.PHONY: backend-migrate
backend-migrate:
	$(ARTISAN) migrate --seed

.PHONY: mobile-setup
mobile-setup:
	cd $(WORKDIR_MOBILE) && $(FLUTTER) pub get && $(FLUTTER) pub run build_runner build --delete-conflicting-outputs

.PHONY: mobile-run
mobile-run:
	cd $(WORKDIR_MOBILE) && $(FLUTTER) run $(DART_DEFINE)

.PHONY: mobile-analyze
mobile-analyze:
	cd $(WORKDIR_MOBILE) && $(FLUTTER) analyze

.PHONY: mobile-test
mobile-test:
	cd $(WORKDIR_MOBILE) && $(FLUTTER) test
