<?php

namespace Lapis;

final class Dirs
{
  public const string Root = __DIR__ . '/../..';
  public const string App = self::Root . '/backend/app';
  public const string Logs = self::Root . '/logs';
  public const string Temp = self::Root . '/temp';


  private function __construct() {}
}