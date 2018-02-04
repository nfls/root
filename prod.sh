npm install
composer install
node build/build.js
bin/console doctrine:schema:update --force
bin/console cache:clear