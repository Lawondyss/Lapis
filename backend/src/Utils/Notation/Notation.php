<?php

namespace Lapis\Utils\Notation;

use function explode;
use function preg_replace;
use function str_contains;

final class Notation
{
  private function __construct() {}


  public static function asCamelCase(string $string): string
  {
    return self::as(NotationCase::Camel, $string);
  }


  public static function asPascalCase(string $string): string
  {
    return self::as(NotationCase::Pascal, $string);
  }


  public static function asSnakeCase(string $string): string
  {
    return self::as(NotationCase::Snake, $string);
  }


  public static function asKebabCase(string $string): string
  {
    return self::as(NotationCase::Kebab, $string);
  }


  public static function as(NotationCase $case, string $string): string
  {
    $words = self::parse($string);

    return $case->join(...$words);
  }


  /**
   * @param string $string
   * @return string[] String parsed to array of words
   */
  private static function parse(string $string): array
  {
    // adds "-" before all uppercase chars in string
    $string = preg_replace('/(?<!^)[A-Z]/', '-$0', $string);

    return match(true) {
      str_contains($string, '-') => explode('-', $string),
      str_contains($string, '_') => explode('_', $string),
      str_contains($string, '.') => explode('.', $string),
      default => [$string],
    };
  }
}
