sudo git submodule update --init --recursive
sudo npm install
sudo composer install
cd public/assets
sudo bower install --allow-root
cd ../..
node build/build.js
sudo bin/console doctrine:schema:update --force
sudo bin/console cache:clear
