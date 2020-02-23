#!/usr/bin/env php
<?php
use Codedungeon\PHPCliColors\Color;
use Laradock\ComposeGenerator;

define('LARADOCK_ROOT', realpath(__DIR__ . '/..'));

require LARADOCK_ROOT . '/vendor/autoload.php';
require __DIR__ . '/helpers.php';

$templateFile = LARADOCK_ROOT . '/resources/docker-compose.example.yml';
$outputFile = LARADOCK_ROOT . '/var/docker-compose.yml';

$generator = new ComposeGenerator();
$generator->setTemplateFile($templateFile);
$generator->setConfigFile(getConfigFile());
$generator->setOutputFile($outputFile);
$generator->generate();

if (!$generator->write()) {
	throw new RuntimeException('Error writing to: ' . $outputFile);
}

file_put_contents(LARADOCK_ROOT . '/var/shell', $generator->resolveShell());

echo Color::green() . 'Configuration updated.' . Color::reset() . PHP_EOL;
