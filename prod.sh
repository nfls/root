npm install
composer install
cd public/assets
bower install
cd ../..
node build/build.js
bin/console doctrine:schema:update --force
bin/console cache:clear
