#!/bin/bash

# Script de mise à jour pour l'application San2Stic Map
# À exécuter depuis le répertoire racine du projet sur le VPS.

set -e # Arrête le script si une commande échoue

# --- Couleurs pour la sortie ---
C_RESET='\033[0m'
C_RED='\033[0;31m'
C_GREEN='\033[0;32m'
C_BLUE='\033[0;34m'
C_YELLOW='\033[1;33m'

# --- Fonctions utilitaires ---
echo_info() {
    echo -e "${C_BLUE}INFO: $1${C_RESET}"
}

echo_success() {
    echo -e "${C_GREEN}SUCCÈS: $1${C_RESET}"
}

echo_warning() {
    echo -e "${C_YELLOW}ATTENTION: $1${C_RESET}"
}

echo_error() {
    echo -e "${C_RED}ERREUR: $1${C_RESET}" >&2
    exit 1
}

command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# --- Début du script de mise à jour ---
echo_info "Démarrage du script de mise à jour pour San2Stic Map..."

# Vérification que nous sommes dans le bon répertoire
if [ ! -f "artisan" ]; then
    echo_error "Le fichier 'artisan' n'a pas été trouvé dans le répertoire actuel. Veuillez exécuter ce script depuis la racine de votre projet Laravel."
fi

# Récupération du chemin du projet pour les commandes Supervisor
PROJECT_PATH=$(pwd)

echo_info "1. Récupération des dernières modifications du code..."
git pull origin main # Assurez-vous que 'main' est le nom de votre branche principale
echo_success "Code mis à jour."

echo_info "2. Mise à jour des dépendances Composer..."
composer install --optimize-autoloader --no-dev
echo_success "Dépendances Composer mises à jour."

echo_info "3. Mise à jour des dépendances NPM..."
npm install
echo_success "Dépendances NPM mises à jour."

echo_info "4. Recompilation des assets frontend..."
npm run build
echo_success "Assets frontend recompilés."

echo_info "5. Lancement des migrations de base de données..."
php artisan migrate --force
echo_success "Migrations exécutées."

echo_info "6. Vidage et reconstruction des caches Laravel..."
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo_success "Caches Laravel mis à jour."

echo_info "7. Redémarrage des workers de file d'attente (Supervisor)..."
# Cette commande nécessite les droits sudo et que supervisorctl soit dans le PATH
if command_exists "supervisorctl"; then
    sudo supervisorctl restart san2stic-worker:*
    echo_success "Workers Supervisor redémarrés."
else
    echo_warning "Supervisorctl non trouvé. Veuillez redémarrer vos workers manuellement si nécessaire."
fi

echo_success "Application San2Stic Map mise à jour avec succès !"
