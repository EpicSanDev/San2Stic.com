#!/bin/bash

# Script de nettoyage pour l'application San2Stic Map
# Ce script est DESTRUCTEUR et supprime le site et sa configuration.
# À exécuter avec les privilèges sudo.

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

# --- Début du script de nettoyage ---
echo_warning "ATTENTION : Ce script est DESTRUCTEUR et va supprimer votre site et sa configuration !"
echo_warning "Assurez-vous d'avoir des sauvegardes si nécessaire."

read -p "Êtes-vous SÛR de vouloir continuer ? Tapez 'oui' pour confirmer : " CONFIRMATION
if [[ ! "$CONFIRMATION" =~ ^oui$ ]]; then
    echo_info "Opération annulée."
    exit 0
fi

echo_info "Collecte des informations pour le nettoyage..."

while true; do read -p "Nom de domaine du site à supprimer (ex: san2stic.com) : " APP_URL; if [ -n "$APP_URL" ]; then break; else echo_warning "Ne peut pas être vide."; fi; done
while true; do read -p "Chemin absolu du projet à supprimer (ex: /var/www/san2stic-map) : " PROJECT_PATH; if [ -n "$PROJECT_PATH" ]; then break; else echo_warning "Ne peut pas être vide."; fi; done

echo_info "Configuration de la base de données à supprimer :"
while true; do read -p "Nom de la base de données : " DB_DATABASE; if [ -n "$DB_DATABASE" ]; then break; else echo_warning "Ne peut pas être vide."; fi; done
while true; do read -p "Utilisateur de la base de données : " DB_USERNAME; if [ -n "$DB_USERNAME" ]; then break; else echo_warning "Ne peut pas être vide."; fi; done
read -s -p "Mot de passe root de votre serveur MySQL (nécessaire pour supprimer la DB/l'utilisateur) : " MYSQL_ROOT_PASSWORD
echo

# --- Exécution du nettoyage ---

echo_info "1. Arrêt et suppression de la configuration Supervisor..."
if command_exists "supervisorctl"; then
    sudo supervisorctl stop san2stic-worker:* || true # '|| true' pour ne pas arrêter le script si déjà arrêté
    sudo supervisorctl remove san2stic-worker || true
    sudo rm -f /etc/supervisor/conf.d/san2stic-worker.conf || true
    sudo supervisorctl reread || true
    sudo supervisorctl update || true
    echo_success "Configuration Supervisor supprimée."
else
    echo_warning "Supervisorctl non trouvé. Veuillez supprimer la configuration manuellement si nécessaire."
fi

echo_info "2. Suppression de la configuration Nginx..."
if [ -f "/etc/nginx/sites-available/${APP_URL}" ]; then
    sudo rm -f "/etc/nginx/sites-available/${APP_URL}"
    sudo rm -f "/etc/nginx/sites-enabled/${APP_URL}"
    sudo systemctl restart nginx
    echo_success "Configuration Nginx supprimée et service redémarré."
else
    echo_warning "Fichier de configuration Nginx pour ${APP_URL} non trouvé. Ignoré."
fi

echo_info "3. Suppression du répertoire du projet : ${PROJECT_PATH}..."
if [ -d "$PROJECT_PATH" ]; then
    sudo rm -rf "$PROJECT_PATH"
    echo_success "Répertoire du projet supprimé."
else
    echo_warning "Répertoire du projet ${PROJECT_PATH} non trouvé. Ignoré."
fi

echo_info "4. Suppression de la base de données et de l'utilisateur..."
if command_exists "mysql"; then
    MYSQL_COMMANDS="DROP DATABASE IF EXISTS `${DB_DATABASE}`; DROP USER IF EXISTS '${DB_USERNAME}'@'localhost'; FLUSH PRIVILEGES;"
    
    echo "${MYSQL_COMMANDS}" | mysql -u root -p"${MYSQL_ROOT_PASSWORD}"
    
    if [ $? -eq 0 ]; then
        echo_success "Base de données '${DB_DATABASE}' et utilisateur '${DB_USERNAME}' supprimés."
    else
        echo_error "La suppression de la base de données a échoué. Vérifiez le mot de passe root de MySQL et les permissions."
    fi
else
    echo_warning "Commande 'mysql' non trouvée. Veuillez supprimer la base de données manuellement."
fi


echo_warning "5. N'oubliez pas de révoquer/supprimer manuellement les certificats SSL Certbot si vous n'en avez plus besoin pour ce domaine :"
echo "   sudo certbot delete --cert-name ${APP_URL}"

echo_success "Nettoyage terminé ! Votre serveur est maintenant prêt à repartir de zéro pour ce site."
