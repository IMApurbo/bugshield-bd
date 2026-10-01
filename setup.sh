#!/usr/bin/env bash
# BugShield BD - one-shot setup for Kali Linux (Debian based), MySQL/MariaDB database.
# Usage:  chmod +x setup.sh && ./setup.sh
# Options: --no-serve   (do not start the dev server at the end)
# Optional env vars: DB_NAME DB_USER DB_PASS BKASH_NUMBER
set -euo pipefail

HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP="$HERE/bugshield-app"
SERVE=1
[[ "${1:-}" == "--no-serve" ]] && SERVE=0

DB_NAME="${DB_NAME:-bugshield}"
DB_USER="${DB_USER:-bugshield}"
DB_PASS="${DB_PASS:-BugShield_2026_Pass}"

say() { printf '\n\033[1;32m==> %s\033[0m\n' "$*"; }

SUDO=""
if [[ $EUID -ne 0 ]]; then SUDO="sudo"; else export COMPOSER_ALLOW_SUPERUSER=1; fi

say "Installing PHP, MariaDB (MySQL) and tools (apt)"
$SUDO apt-get update -y
$SUDO apt-get install -y php-cli php-mbstring php-xml php-curl php-zip php-mysql php-sqlite3 php-bcmath php-intl \
    mariadb-server unzip curl git ca-certificates

if ! command -v composer >/dev/null 2>&1; then
  say "Installing Composer"
  $SUDO apt-get install -y composer || {
    curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php
    $SUDO php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
  }
fi

php -r 'exit(version_compare(PHP_VERSION,"8.2.0",">=")?0:1);' || { echo "PHP 8.2+ is required."; exit 1; }

say "Starting MySQL (MariaDB) and creating the database"
$SUDO systemctl enable --now mariadb 2>/dev/null || $SUDO service mariadb start || $SUDO service mysql start
for i in {1..20}; do $SUDO mysql -e "SELECT 1" >/dev/null 2>&1 && break; sleep 1; done
$SUDO mysql <<SQL
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
ALTER USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SQL

if [[ ! -f "$APP/artisan" ]]; then
  say "Creating a fresh Laravel project (downloads packages, takes a few minutes)"
  composer create-project laravel/laravel "$APP" --prefer-dist --no-interaction
fi

say "Copying BugShield BD source into the Laravel project"
cp -a "$HERE/overlay/." "$APP/"
cd "$APP"

say "Configuring .env (MySQL, sync queue)"
[[ -f .env ]] || cp .env.example .env
set_env() { # key value
  if grep -qE "^#?\s*$1=" .env; then sed -i -E "s|^#?\s*$1=.*|$1=$2|" .env; else echo "$1=$2" >> .env; fi
}
set_env APP_NAME '"BugShield BD"'
set_env DB_CONNECTION mysql
set_env DB_HOST 127.0.0.1
set_env DB_PORT 3306
set_env DB_DATABASE "$DB_NAME"
set_env DB_USERNAME "$DB_USER"
set_env DB_PASSWORD "$DB_PASS"
set_env QUEUE_CONNECTION sync
set_env SESSION_DRIVER database
set_env BKASH_NUMBER "${BKASH_NUMBER:-01700000000}"
set_env BKASH_TYPE Personal
php artisan key:generate --force
php artisan config:clear

say "Building the database tables and demo data"
php artisan migrate:fresh --seed --force
php artisan storage:link || true
php artisan optimize:clear

cat <<MSG

------------------------------------------------------------
 BugShield BD is ready.   Project folder: $APP
 Database: MySQL/MariaDB  db=$DB_NAME  user=$DB_USER  pass=$DB_PASS

 Admin        admin@bugshield.bd    / Admin@12345
 Researcher   researcher@demo.bd    / password
 Organization org@demo.bd           / password

 Change the bKash number on the org registration form:
   edit BKASH_NUMBER in $APP/.env  then: php artisan config:clear

 Start later with:  sudo service mariadb start
                    cd "$APP" && php artisan serve
------------------------------------------------------------
MSG

if [[ $SERVE -eq 1 ]]; then
  say "Starting server at http://127.0.0.1:8000  (Ctrl+C to stop)"
  php artisan serve --host=127.0.0.1 --port=8000
fi
