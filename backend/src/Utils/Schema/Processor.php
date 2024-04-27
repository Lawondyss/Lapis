<?php

namespace Lapis\Utils\Schema;

use function is_array;

class Processor
{
  private function __construct() {}


  /**
   * @return mixed[]
   * @throws Exception\ValidateException
   */
  public static function process(mixed $data, Schema $schema): array
  {
    $normalized = $schema->normalize($data);
    $default = $schema->default();

    if (is_array($normalized) && is_array($default)) {
      $normalized += $default;
    }

    return $normalized ?? $default;
  }
}
