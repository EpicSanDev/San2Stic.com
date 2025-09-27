#!/bin/bash

# Script de déploiement interactif pour l'application San2Stic Map
# Ce script doit être exécuté avec les privilèges sudo si vous souhaitez automatiser
# le déplacement des fichiers de configuration et le redémarrage des services.

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
echo_info "Démarrage du script de déploiement pour San2Stic Map..."

# 1. Vérification des dépendances
echo_info "Vérification des dépendances requises..."
DEPS=("git" "composer" "npm" "php" "nginx" "supervisorctl" "ffmpeg")
for cmd in "${DEPS[@]}"; do
    if command_exists "$cmd"; then
        echo_success "- Commande '$cmd' trouvée."
    else
        echo_error "Commande '$cmd' non trouvée. Veuillez l'installer avant de continuer."
    fi
done

# 2. Collecte des informations
echo_info "Veuillez fournir les informations suivantes pour la configuration :"

read -p "URL de votre dépôt Git (ex: https://github.com/user/repo.git) : " GIT_REPO
read -p "Nom de domaine (ex: san2stic.com) : " APP_URL
read -p "Version de PHP installée sur le VPS (ex: 8.3) : " PHP_VERSION
read -p "Chemin absolu du projet (ex: /var/www/san2stic-map) : " PROJECT_PATH

echo_info "Configuration de la base de données :"
read -p "Nom de la base de données : " DB_DATABASE
read -p "Utilisateur de la base de données : " DB_USERNAME
read -s -p "Mot de passe de la base de données : " DB_PASSWORD
echo

echo_warning "Les informations pour Pusher, AWS S3 et Mail peuvent être laissées vides pour le moment."
echo_warning "Vous devrez les remplir manuellement dans le fichier .env plus tard."
read -p "Pusher App ID : " PUSHER_APP_ID
read -p "Pusher App Key : " PUSHER_APP_KEY
read -p "Pusher App Secret : " PUSHER_APP_SECRET
read -p "Pusher App Cluster : " PUSHER_APP_CLUSTER
read -p "AWS Access Key ID : " AWS_ACCESS_KEY_ID
read -p "AWS Secret Access Key : " AWS_SECRET_ACCESS_KEY
read -p "AWS Default Region : " AWS_DEFAULT_REGION
read -p "AWS Bucket Name : " AWS_BUCKET

# --- Exécution du déploiement ---

# 2.5. Création de la base de données (Optionnel)
read -p "Voulez-vous que ce script tente de créer la base de données et l'utilisateur ? (MySQL/MariaDB requis) [y/N] " CREATE_DB
if [[ "$CREATE_DB" =~ ^[yY](es)?$ ]]; then
    read -s -p "Veuillez entrer le mot de passe root de votre serveur MySQL : " MYSQL_ROOT_PASSWORD
    echo
    echo_info "Tentative de création de la base de données et de l'utilisateur..."
    
    MYSQL_COMMANDS="CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\`; CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}'; GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'localhost'; FLUSH PRIVILEGES;"
    
    mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "${MYSQL_COMMANDS}"
    
    if [ $? -eq 0 ]; then
        echo_success "Base de données et utilisateur créés avec succès."
    else
        echo_error "La création de la base de données a échoué. Veuillez vérifier le mot de passe root de MySQL et que le serveur est bien en cours d'exécution. Vous pouvez créer la base de données manuellement."
    fi
fi

echo_info "Clonage du projet depuis le dépôt Git..."
mkdir -p "$(dirname "$PROJECT_PATH")"
cd "$(dirname "$PROJECT_PATH")"
if [ -d "$PROJECT_PATH" ]; then
    echo_warning "Le répertoire du projet existe déjà. Pull des derniers changements..."
    cd "$PROJECT_PATH"
    git pull
else
    git clone "$GIT_REPO" "$PROJECT_PATH"
    cd "$PROJECT_PATH"
fi

echo_info "Création et configuration du fichier .env..."
cp .env.example .env

# Remplacement des variables dans .env
sed -i "s|^APP_URL=.*|APP_URL=https://${APP_URL}|g" .env
sed -i 's|^APP_ENV=.*|APP_ENV=production|g' .env
sed -i 's|^APP_DEBUG=.*|APP_DEBUG=false|g' .env

sed -i 's|^DB_CONNECTION=.*|DB_CONNECTION=mysql|g' .env
sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|g" .env
sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|g" .env
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|g" .env

sed -i "s|^PUSHER_APP_ID=.*|PUSHER_APP_ID=${PUSHER_APP_ID}|g" .env
sed -i "s|^PUSHER_APP_KEY=.*|PUSHER_APP_KEY=${PUSHER_APP_KEY}|g" .env
sed -i "s|^PUSHER_APP_SECRET=.*|PUSHER_APP_SECRET=${PUSHER_APP_SECRET}|g" .env
sed -i "s|^PUSHER_APP_CLUSTER=.*|PUSHER_APP_CLUSTER=${PUSHER_APP_CLUSTER}|g" .env

sed -i "s|^AWS_ACCESS_KEY_ID=.*|AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}|g" .env
sed -i "s|^AWS_SECRET_ACCESS_KEY=.*|AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}|g" .env
sed -i "s|^AWS_DEFAULT_REGION=.*|AWS_DEFAULT_REGION=${AWS_DEFAULT_REGION}|g" .env
sed -i "s|^AWS_BUCKET=.*|AWS_BUCKET=${AWS_BUCKET}|g" .env

echo_info "Génération de la clé d'application..."
php artisan key:generate

echo_info "Installation des dépendances Composer..."
composer install --optimize-autoloader --no-dev

echo_info "Installation des dépendances NPM et compilation des assets..."
npm install
npm run build

echo_info "Lancement des migrations de la base de données..."
php artisan migrate --force

echo_info "Création du lien de stockage..."
php artisan storage:link

echo_info "Mise en cache des configurations pour la production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# --- Génération des fichiers de configuration ---

NGINX_CONF_FILE="${PROJECT_PATH}/nginx_config"
echo_info "Génération du fichier de configuration Nginx : ${NGINX_CONF_FILE}"

cat <<EOF > "$NGINX_CONF_FILE"
server {
    listen 80;
    server_name ${APP_URL};
    root ${PROJECT_PATH}/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php${PHP_VERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

SUPERVISOR_CONF_FILE="${PROJECT_PATH}/supervisor_config"
echo_info "Génération du fichier de configuration Supervisor : ${SUPERVISOR_CONF_FILE}"

cat <<EOF > "$SUPERVISOR_CONF_FILE"
[program:san2stic-worker]
process_name=%(program_name)s_%(process_num)02d
command=php ${PROJECT_PATH}/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=${PROJECT_PATH}/storage/logs/worker.log
EOF

# --- Instructions finales ---

echo_success "Le déploiement initial est terminé !"
echo_warning "Veuillez maintenant effectuer les étapes manuelles suivantes avec les droits sudo :"

echo "1. Si vous n'avez pas utilisé l'option de création automatique, assurez-vous que la base de données '${DB_DATABASE}' et l'utilisateur '${DB_USERNAME}' existent."
echo "2. Déplacez le fichier de configuration Nginx :"
echo "   sudo mv ${NGINX_CONF_FILE} /etc/nginx/sites-available/${APP_URL}"
echo "3. Activez le site :"
echo "   sudo ln -s /etc/nginx/sites-available/${APP_URL} /etc/nginx/sites-enabled/"
echo "4. Déplacez le fichier de configuration Supervisor :"
echo "   sudo mv ${SUPERVISOR_CONF_FILE} /etc/supervisor/conf.d/san2stic-worker.conf"
echo "5. Appliquez les permissions de fichiers correctes :"
echo "   sudo chown -R www-data:www-data ${PROJECT_PATH}"
echo "   sudo chmod -R 775 ${PROJECT_PATH}/storage ${PROJECT_PATH}/bootstrap/cache"
echo "6. Redémarrez les services :"
echo "   sudo systemctl restart nginx"
echo "   sudo supervisorctl reread"
echo "   sudo supervisorctl update"
echo "   sudo supervisorctl start san2stic-worker:*"
echo "7. (Recommandé) Sécurisez votre site avec un certificat SSL :"
echo "   sudo apt install certbot python3-certbot-nginx"
echo "   sudo certbot --nginx -d ${APP_URL}"

echo_info "Fin du script."
