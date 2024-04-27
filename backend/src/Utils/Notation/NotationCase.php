<?php

namespace Lapis\Utils\Notation;

use function array_map;
use function array_shift;
use function implode;

enum NotationCase
{
  case Camel;
  case Pascal;
  case Snake;
  case Kebab;


  public function join(string ...$words): string
  {
    return match($this) {
      self::Camel => $this->asCamel($words),
      self::Pascal => $this->asPascal($words),
      self::Snake => $this->asSnake($words),
      self::Kebab => $this->asKebab($words),
    };
  }


  private function asCamel(array $words): string
  {
    $first = array_shift($words);
    $others = array_map(ucfirst(...), $words);

    return implode('', [$first, ...$others]);
  }


  private function asPascal(array $words): string
  {
    $words = array_map(ucfirst(...), $words);

    return implode('', $words);
  }


  private function asSnake(array $words): string
  {
    $words = array_map(strtolower(...), $words);

    return implode('_', $words);
  }


  private function asKebab(array $words): string
  {
    $words = array_map(strtolower(...), $words);

    return implode('-', $words);
  }
}
