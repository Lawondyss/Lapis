<?php

namespace Lapis\Utils\Schema\Element;

use Lapis\Utils\Schema\Exception\ValidateException;
use Lapis\Utils\Schema\Schema;
use function is_array;
use function is_object;

class Collection extends Type
{
  public function __construct(
    private readonly Schema $type,
    ?array $default = null,
  ) {
    parent::__construct($default);
  }


  /**
   * @param mixed $value
   * @return mixed[]
   * @throws ValidateException
   */
  public function normalize(mixed $value): array
  {
    $value = parent::normalize($value);

    if (!isset($value)) {
      return [];
    }

    if (is_object($value)) {
      $value = (array)$value;
    }

    if (!is_array($value)) {
      throw new ValidateException('Must be array');
    }

    $data = [];

    foreach ($value as $val) {
      $data[] = $this->type->normalize($val);
    }

    return $data;
  }
}
