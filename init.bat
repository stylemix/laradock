@echo off

copy /-y resources/config.example.yml config.yml
copy /-y resources/env.example .env
copy /-y resources/my.example.cnf etc/my.cnf
copy /-y resources/composer-auth.example.json etc/composer-auth.json
copy /-y resources/xdebug.example.ini etc/xdebug.ini

echo Laradock initialized!
