#!/usr/bin/env sh
cd $(dirname "$0")
pwd -P

wget https://github.com/ryancramerdesign/ProcessWire/archive/devns.zip 
unzip ./devns.zip > /dev/null 2>&1
rm -f ./devns.zip

mv ./ProcessWire-devns/* .
rm -rf ./ProcessWire-devns/

./install_json

echo "ini_set('display_errors', 1);" >> ./site/config.php
echo "ini_set('display_startup_errors', 1);" >> ./site/config.php
echo "error_reporting(E_ALL);" >> ./site/config.php
echo "\$config->debug = true;" >> ./site/config.php

rsync -av ../../Migrations ./site/modules --exclude tests --exclude vendor --exclude docs --exclude migrations --exclude spec --exclude .git

# Spaces around Process are to prevent replacement for ProcessMigrations
# sed -i 's/ Process / ProcessWire\\Process /g' ./site/modules/Migrations/ProcessMigrations.module
# sed -i 's/WireException/ProcessWire\\WireException/g' ./site/modules/Migrations/ProcessMigrations.module
# sed -i 's/WireData/ProcessWire\\WireData/g' ./site/modules/Migrations/Migrations.module
# sed -i 's/Module/ProcessWire\\Module/g' ./site/modules/Migrations/Migrations.module
# sed -i 's/WireException/ProcessWire\\WireException/g' ./site/modules/Migrations/Migrations.module

cp -R ../spec/migrations ./site