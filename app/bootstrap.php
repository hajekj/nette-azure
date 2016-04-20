<?php

require __DIR__ . '/../vendor/autoload.php';
require 'Security/AzureIdentity.php';

if (file_exists('../localConfig.php')) require '../localConfig.php';

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
if (file_exists(__DIR__ . '/config/config.local.neon')) $configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

if (!in_array($_SERVER['REMOTE_ADDR'],['127.0.0.1','::1'])) {
	// Quick fix for HTTPS
	\Nette\Application\Routers\Route::$defaultFlags |= \Nette\Application\IRouter::SECURED;
}

return $container;
