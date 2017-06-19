<?php declare(strict_types=1);

namespace App\Tests;

use Nette\Configurator;
use Nette\DI\Container;

final class ContainerFactory
{
    public function create(): Container
    {
        $configurator = new Configurator;
        $configurator->setDebugMode(true);
        $configurator->setTempDirectory(__DIR__ . '/../temp');

        $configurator->addConfig(__DIR__ . '/../app/config/config.neon');
        $localConfig = __DIR__ . '/../app/config/config.local.neon';
        if (file_exists($localConfig)) {
            $configurator->addConfig($localConfig);
        }
        $configurator->addConfig(__DIR__ . '/config/config.test.neon');

        return $configurator->createContainer();
    }
}
