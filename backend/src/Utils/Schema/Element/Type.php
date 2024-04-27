<?php

namespace Lapis\Utils\Schema\Element;

use Lapis\Utils\Schema\Exception\ValidateException;
use Lapis\Utils\Schema\Schema;
use Nette\Utils\Strings;
use function is_string;

class Type implements Schema
{
  public bool $required = false;

  protected bool $nullable = false;


  public function __construct(
    private readonly mixed $default = null,
  ) {
  }


  public function required(): static
  {
    $this->required = true;

    return $this;
  }


  public function nullable(): static
  {
    $this->nullable = true;

    return $this;
  }


  public function default(): mixed
  {
    return $this->default;
  }


  /**
   * @throws ValidateException
   */
  public function normalize(mixed $value): mixed
  {
    if (!isset($value) && !$this->nullable) {
      throw new ValidateException('Cannot be null');
    }

    return is_string($value) ? Strings::trim($value) : $value;
  }
}
