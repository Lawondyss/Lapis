<?php

namespace Lapis\DI;

use Lapis\Dirs;
use Nette\DI\CompilerExtension;
use Nette\Utils\Finder;
use ReflectionClass;
use function array_pop;
use function class_exists;
use function explode;
use function implode;
use function str_replace;
use function strlen;
use function substr;
use const DIRECTORY_SEPARATOR;

final class ControllersExtension extends CompilerExtension
{
  public const string Prefix = 'endpoint.';


  public function loadConfiguration(): void
  {
    $builder = $this->getContainerBuilder();

    foreach ($this->findControllers() as $name => $class) {
      $builder->addDefinition($this::Prefix . $name)->setType($class);
    }
  }


  /**
   * @return array<string, string>
   */
  private function findControllers(): array
  {
    $controllers = [];
    $prefixLength = strlen(Dirs::App) + 1;

    foreach (Finder::findFiles(Dirs::App . '/Endpoint/**/*Controller.php') as $file) {
      // Removes prefix with path and suffix with .php
      $classFile = substr($file, $prefixLength, -4);

      // App is the root namespace and the class adheres to PSR-4
      $class = 'App\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $classFile);

      if (!class_exists($class) || (new ReflectionClass($class))->isAbstract()) {
        continue;
      }

      $parts = array_map(lcfirst(...), explode(DIRECTORY_SEPARATOR, $classFile));

      // First (endpoint) and last (*Controller) parts not needed
      array_shift($parts);
      array_pop($parts);

      $controllers[implode('.', $parts)] = $class;
    }

    return $controllers;
  }
}