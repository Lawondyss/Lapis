<?php

namespace App\Core;

use Lapis\Routing\RouteList;

final class RouterFactory
{
  private function __construct() {}


  public static function create(): RouteList
  {
    $router = new RouteList;

    $admin = $router->withModule('Admin');
    $admin->addRoute('Recipe:?:');

    $router->addRoute('Auth:');
    $router->addRoute('Info:+');
    $router->addRoute('<controller>[/<id>]');

    return $router;
  }
}