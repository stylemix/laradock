#!/usr/bin/env bash

cp -i resources/config.example.yml config.yml
cp -i resources/env.example .env
cp -i resources/my.example.cnf etc/my.cnf
cp -i resources/composer-auth.example.json etc/composer-auth.json
cp -i resources/xdebug.example.ini etc/xdebug.ini

echo "Laradock initialized!"
