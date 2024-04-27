<?php

namespace Lapis\DI;

use Nette\Bootstrap\Configurator as NetteConfigurator;
use Nette\Bootstrap\Extensions\ConstantsExtension;
use Nette\Bootstrap\Extensions\PhpExtension;
use Nette\Bridges\CacheDI\CacheExtension;
use Nette\Bridges\DatabaseDI\DatabaseExtension;
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\Bridges\HttpDI\SessionExtension;
use Nette\DI\Extensions\DecoratorExtension;
use Nette\DI\Extensions\DIExtension;
use Nette\DI\Extensions\ExtensionsExtension;
use Nette\DI\Extensions\SearchExtension;
use Tracy\Bridges\Nette\TracyExtension;

final class Configurator extends NetteConfigurator
{
  public array $defaultExtensions = [
    'cache' => [CacheExtension::class, ['%tempDir%/cache']],
    'constants' => ConstantsExtension::class,
    'database' => [DatabaseExtension::class, ['%debugMode%']],
    'decorator' => DecoratorExtension::class,
    'di' => [DIExtension::class, ['%debugMode%']],
    'extensions' => ExtensionsExtension::class,
    'http' => [HttpExtension::class, ['%consoleMode%']],
    'php' => PhpExtension::class,
    'search' => [SearchExtension::class, ['%tempDir%/cache/nette.search']],
    'session' => [SessionExtension::class, ['%debugMode%', '%consoleMode%']],
    'tracy' => [TracyExtension::class, ['%debugMode%', '%consoleMode%']],
    'app' => [AppExtension::class, ['%debugMode%']],
    'controllers' => ControllersExtension::class,
  ];
}