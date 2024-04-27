<?php

namespace Lapis\Utils\Schema;

use Lapis\Utils\Schema\Exception\ValidateException;

interface Schema
{
  /**
   * @throws ValidateException
   */
  public function normalize(mixed $value): mixed;


  public function default(): mixed;
}
