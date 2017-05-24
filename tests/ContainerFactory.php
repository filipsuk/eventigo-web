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
        $configurator->addConfig(__DIR__ . '/../app/config/config.local.neon');

        return $configurator->createContainer();
    }
}
