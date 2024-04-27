<?php

namespace Lapis\Routing;

class RouterFactory
{
  public static function create(): RouteList
  {
    $router = new RouteList;

    $router->addRoute('<controller>[/<id>]');

    return $router;
  }
}