#!/bin/bash

# Script de mise à jour de la configuration S3 Hetzner dans le fichier .env
# À exécuter depuis n'importe où, il demandera le chemin du projet.

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

# --- Début du script ---
echo_info "Démarrage du script de mise à jour de la configuration S3 Hetzner..."

# 1. Collecte des informations
echo_info "Veuillez fournir les informations suivantes pour la configuration S3 Hetzner :"

while true; do read -p "Chemin absolu du projet Laravel (ex: /var/www/san2stic-map) : " PROJECT_PATH; if [ -n "$PROJECT_PATH" ]; then break; else echo_warning "Ne peut pas être vide."; fi; done

if [ ! -d "$PROJECT_PATH" ]; then
    echo_error "Le répertoire du projet '${PROJECT_PATH}' n'existe pas."
fi

if [ ! -f "${PROJECT_PATH}/.env" ]; then
    echo_error "Le fichier .env n'existe pas dans '${PROJECT_PATH}'. Veuillez vous assurer que le projet est déployé."
fi

read -p "Hetzner Access Key ID : " AWS_ACCESS_KEY_ID
read -p "Hetzner Secret Access Key : " AWS_SECRET_ACCESS_KEY
read -p "Hetzner Region (ex: fsn1) : " AWS_DEFAULT_REGION
read -p "Hetzner Bucket Name : " AWS_BUCKET
read -p "Hetzner S3 Endpoint (ex: https://fsn1.your-objectstorage.com) : " AWS_ENDPOINT

# 2. Mise à jour du fichier .env
echo_info "Mise à jour du fichier .env dans ${PROJECT_PATH}..."

cd "$PROJECT_PATH"

sed -i "s|^AWS_ACCESS_KEY_ID=.*|AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}|g" .env
sed -i "s|^AWS_SECRET_ACCESS_KEY=.*|AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}|g" .env
sed -i "s|^AWS_DEFAULT_REGION=.*|AWS_DEFAULT_REGION=${AWS_DEFAULT_REGION}|g" .env
sed -i "s|^AWS_BUCKET=.*|AWS_BUCKET=${AWS_BUCKET}|g" .env
sed -i "s|^AWS_ENDPOINT=.*|AWS_ENDPOINT=${AWS_ENDPOINT}|g" .env
sed -i "s|^AWS_USE_PATH_STYLE_ENDPOINT=.*|AWS_USE_PATH_STYLE_ENDPOINT=true|g" .env
sed -i "s|^FILESYSTEM_DISK=.*|FILESYSTEM_DISK=s3|g" .env

echo_success "Fichier .env mis à jour avec la configuration Hetzner S3."

# 3. Vidage et reconstruction du cache de configuration Laravel
echo_info "Vidage et reconstruction du cache de configuration Laravel..."
php artisan config:clear
php artisan config:cache
echo_success "Cache de configuration Laravel mis à jour."

echo_success "Configuration S3 Hetzner mise à jour avec succès !"
