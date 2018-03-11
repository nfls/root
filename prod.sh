git submodule update --init --recursive --remote
npm install
composer install
cd public/assets
bower install --allow-root
cd ../..
node build/build.js
bin/console doctrine:schema:update --force
bin/console cache:clear
app/console cache:warmup --env=prod
