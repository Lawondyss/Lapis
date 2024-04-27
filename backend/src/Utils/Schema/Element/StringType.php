<?php

namespace Lapis\Utils\Schema\Element;

use Lapis\Utils\Schema\Exception\ValidateException;
use Nette\Utils\Strings;

class StringType extends Type
{
  private ?int $min = null;

  private ?int $max = null;


  public function __construct(?string $default = null)
  {
    parent::__construct($default);
  }


  public function min(int $min): static
  {
    $this->min = $min;

    return $this;
  }


  public function max(int $max): static
  {
    $this->max = $max;

    return $this;
  }


  /**
   * @throws ValidateException
   */
  public function normalize(mixed $value): ?string
  {
    $value = parent::normalize($value);

    if (!isset($value)) {
      return null;
    }

    if (!is_string($value)) {
      throw new ValidateException('Must be string');
    }

    $length = Strings::length($value);

    if ($length === 0 && $this->required) {
      throw new ValidateException('Must not be empty string');
    } elseif (isset($this->min) && isset($this->max) && !($length >= $this->min && $length <= $this->max)) {
      throw new ValidateException(sprintf('Length must be between %d and %d', $this->min, $this->max));
    } elseif (isset($this->min) && $length < $this->min) {
      throw new ValidateException(sprintf('Length must be equal or greater than %d', $this->min));
    } elseif (isset($this->max) && $length > $this->max) {
      throw new ValidateException(sprintf('Length must be equal or less than %d', $this->max));
    }

    return $value;
  }
}
