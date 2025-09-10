include .env.local

SSH = ssh -i $(SSH_KEY_PATH) $(USER)@$(HOST)

deploy-prod: access assets

# Suppression du dossier public/assets
reset-asset:
	@echo
	@echo "Reset dossier public/assets"
	$(SSH) "cd $(REMOTE_DIR) && rm -rf public/assets"

access: reset-asset
	@echo
	@echo "🔐 Connexion au serveur O2switch"
	$(SSH) "cd $(REMOTE_DIR) && git pull origin $(GIT_BRANCH)"

dependencies:
	@echo
	@echo "📦 Installation des dépendances"
	$(SSH) "cd $(REMOTE_DIR) && composer install --no-interaction --no-dev --optimize-autoloader"
	

tailwindss:
	@echo
	@echo "====== Build Tailwind CSS ======"
	$(SSH) "cd $(REMOTE_DIR) && php bin/console tailwind:build"
	

# Compilation des assets (après installation des dépendances)
assets: dependencies tailwindss
	@echo
	@echo "====== Build asset ======"
	$(SSH) "cd $(REMOTE_DIR) && php bin/console asset-map:compile"
	@echo "====== Deploying with success ✅ ======"