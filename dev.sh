#sudo service mysql start
#sudo service redis-server start
bin/console server:start 0.0.0.0:8000
./node_modules/.bin/encore dev --watch
