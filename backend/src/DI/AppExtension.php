<?php

namespace Lapis\DI;

use Lapis\App\Kernel;
use Lapis\Resource\ResourceFactory;
use Lapis\Routing\RouterFactory;
use Lapis\Security\JWT\Generator;
use Lapis\Security\JWT\Validator;
use Lapis\Security\User;
use Lapis\Utils\DotEnv\DotEnv;
use Nette\DI\CompilerExtension;

final class AppExtension extends CompilerExtension
{
  public function __construct(
    private readonly bool $isDebugMode = false,
  ) {}


  public function loadConfiguration(): void
  {
    $builder = $this->getContainerBuilder();

    $builder->addDefinition($this->prefix('kernel'))->setCreator(Kernel::class, [$this->isDebugMode]);
    $builder->addDefinition($this->prefix('resourceFactory'))->setCreator(ResourceFactory::class);
    $builder->addDefinition($this->prefix('router'))->setCreator(RouterFactory::class . '::create');
    $builder->addDefinition($this->prefix('user'))->setCreator(User::class);
    $builder->addDefinition($this->prefix('jwt.generator'))->setCreator(Generator::class);
    $builder->addDefinition($this->prefix('jwt.validator'))->setCreator(Validator::class);
    $builder->addDefinition($this->prefix('env'))->setCreator(DotEnv::class);
  }
}