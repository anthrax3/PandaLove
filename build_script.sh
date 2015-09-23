#!/usr/bin/env bash
composer install;
npm install;
npm install gulp;
mysql --user=connor_testing --password=gmsZbtvEhEM9 -BNe "show tables" connor_testing | tr '\n' ',' | sed -e 's/,$//' | awk '{print "SET FOREIGN_KEY_CHECKS = 0;DROP TABLE IF EXISTS " $1 ";SET FOREIGN_KEY_CHECKS = 1;"}' | mysql --user=connor_testing --password=gmsZbtvEhEM9 connor_testing;
./artisan migrate --env=testing;
./artisan db:seed --env=testing;
gulp
phpunit;