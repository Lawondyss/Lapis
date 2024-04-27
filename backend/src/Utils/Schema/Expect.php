<?php

namespace Lapis\Utils\Schema;

use DateTimeInterface;
use Lapis\Utils\Schema\Element\Collection;
use Lapis\Utils\Schema\Element\DateType;
use Lapis\Utils\Schema\Element\NumberType;
use Lapis\Utils\Schema\Element\StringType;
use Lapis\Utils\Schema\Element\Structure;
use Lapis\Utils\Schema\Element\Type;

final class Expect
{
  /**
   * @param array<string, Type> $items
   */
  public static function structure(array $items): Structure
  {
    return new Structure($items);
  }


  public static function collection(Schema $type, ?array $default = null): Collection
  {
    return new Collection($type, $default);
  }


  public static function int(?int $default = null): NumberType
  {
    return new NumberType($default);
  }


  public static function string(?string $default = null): StringType
  {
    return new StringType($default);
  }


  public static function date(?DateTimeInterface $default = null): DateType
  {
    return new DateType($default);
  }
}
