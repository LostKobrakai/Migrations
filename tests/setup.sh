#!/usr/bin/env sh

cd $(dirname "$0")
DIR = pwd -P
pwd -P

if [ -f "master-backup.zip" ]
then
  unzip ./master-backup.zip > /dev/null 2>&1
else
  wget https://github.com/ryancramerdesign/pw28/archive/master.zip
  unzip ./master.zip
  rm -f ./master.zip
fi

mv ./pw28-master/* .
rm -rf ./pw28-master/

./install_json

echo "ini_set('display_errors', 1);" >> ./site/config.php
echo "ini_set('display_startup_errors', 1);" >> ./site/config.php
echo "error_reporting(E_ALL);" >> ./site/config.php
echo "\$config->debug = true;" >> ./site/config.php

if [ "${TRAVIS}" = "true" ]
then
  echo "TRAVIS";
  sed -i \
    's|$corePreloads =|if\(defined\("PROCESSWIRE_TEST_RUNNING"\)\) return;\n$corePreloads =|' \
    wire/core/boot.php
  sed -i \
    's|\$this->includeModuleFile(\$pathname, \$class);|if(!class_exists(\$class)) \$this->includeModuleFile(\$pathname, \$class);|g' \
    wire/core/Modules.php
else
  echo "NOT-TRAVIS";
  sed -i '' 's|$corePreloads =|if\(defined\("PROCESSWIRE_TEST_RUNNING"\)\) return;\
  $corePreloads =|' ./wire/core/boot.php
  sed -i '' 's|\$this->includeModuleFile(\$pathname, \$class);|if(!class_exists(\$class)) \$this->includeModuleFile(\$pathname, \$class);|g' ./wire/core/Modules.php
fi

rsync -av ../../Migrations ./site/modules \
  --exclude tests \
  --exclude vendor \
  --exclude docs \
  --exclude migrations \
  --exclude spec \
  --exclude .git

# Spaces around Process are to prevent replacement for ProcessMigrations
# sed -i 's/ Process / ProcessWire\\Process /g' ./site/modules/Migrations/ProcessMigrations.module
# sed -i 's/WireException/ProcessWire\\WireException/g' ./site/modules/Migrations/ProcessMigrations.module
# sed -i 's/WireData/ProcessWire\\WireData/g' ./site/modules/Migrations/Migrations.module
# sed -i 's/Module/ProcessWire\\Module/g' ./site/modules/Migrations/Migrations.module
# sed -i 's/WireException/ProcessWire\\WireException/g' ./site/modules/Migrations/Migrations.module

cp -R ../spec/migrations ./site