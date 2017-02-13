<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

if (PHP_SAPI !== "cli") {
	$configurator->setDebugMode(in_array($_SERVER['HTTP_HOST'], ['localhost:8000', 'kuba.eventigo.cz', 'filip.eventigo.cz', 'eventigo.local', 'eventigo.local.cz']));
}

// Fix redirects to port 80 (https://forum.nette.org/cs/13896-openshift-redirect-z-https-pridava-port-80)
if ( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ) {
	$_SERVER['SERVER_PORT'] = 443;
}

$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

return $configurator->createContainer();
