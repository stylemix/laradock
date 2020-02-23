#!/usr/bin/env php
<?php
use Codedungeon\PHPCliColors\Color;

define('LARADOCK_ROOT', realpath(__DIR__ . '/..'));
const LABEL_PAD = 20;

require LARADOCK_ROOT . '/vendor/autoload.php';
require __DIR__ . '/helpers.php';

$config = \Laradock\Config::fromFile(getConfigFile());

// PMA
echo str_repeat('=', LABEL_PAD / 2) . PHP_EOL;
$ports = $config->getServicePorts('phpmyadmin');
foreach ($ports as $port) {
	echo Color::cyan() . str_pad('PhpMyAdmin:', LABEL_PAD) . Color::reset() . 'http://localhost:' . $port . PHP_EOL;
}

// Mailhog
$ports = $config->getServicePorts('mailhog');
foreach ($ports as $port) {
	echo Color::cyan() . str_pad('Mailhog:', LABEL_PAD) . Color::reset() . 'http://localhost:' . $port . PHP_EOL;
}

// ElasticSearch
foreach (['6', '7'] as $version) {
	if (in_array($version, $config->get('features.elasticsearch', []))) {
		$ports = $config->getServicePorts("elasticsearch{$version}");
		foreach ($ports as $port) {
			echo Color::cyan() . str_pad("ElasticSearch {$version}:", LABEL_PAD) . Color::reset() . 'http://localhost:' . $port . PHP_EOL;
		}
	}
}

// Sites
echo str_repeat('=', LABEL_PAD / 2) . PHP_EOL;
echo Color::cyan() . str_pad('Sites: ', LABEL_PAD) . Color::reset();
$ports = $config->getServicePorts('nginx');
foreach ($config->get('sites') as $i => $site) {
	echo ($i > 0 ? str_pad('', LABEL_PAD) : ''), "http://{$site['map']}:{$ports[0]}" . PHP_EOL;
}
