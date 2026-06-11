#! /bin/sh

apt-get update
apt-get install -y libzip-dev zip sqlite3
docker-php-ext-install zip
if [ ! -f "/usr/local/fsdbtools/backend/db.sqlite" ]; then cd ../backend && cat db-init.sql | sqlite3 db.sqlite; fi
apachectl graceful
