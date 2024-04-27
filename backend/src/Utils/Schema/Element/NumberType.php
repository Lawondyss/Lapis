<?php

namespace Lapis\Utils\Schema\Element;

use Lapis\Utils\Schema\Exception\ValidateException;
use Nette\Utils\Validators;

class NumberType extends Type
{
  private ?float $min = null;

  private ?float $max = null;


  public function __construct(int|float|null $default = null)
  {
    parent::__construct($default);
  }


  public function min(float $min): static
  {
    $this->min = $min;

    return $this;
  }


  public function max(float $max): static
  {
    $this->max = $max;

    return $this;
  }


  /**
   * @throws ValidateException
   */
  public function normalize(mixed $value): int|float|null
  {
    $value = parent::normalize($value);

    if (!isset($value)) {
      return null;
    }

    if (!Validators::isNumeric($value)) {
      throw new ValidateException('Must be number');
    }

    // cast to int|float
    $value *= 1;

    $value = (float)$value == (int)$value ? (int)$value : (float)$value;
    $min = isset($this->min) && $this->min == (int)$this->min ? (int)$this->min : $this->min;
    $max = isset($this->max) && $this->max == (int)$this->max ? (int)$this->max : $this->max;

    if (isset($min) && isset($max) && !Validators::isInRange($value, [$min, $max])) {
      throw new ValidateException(sprintf('Must be between %s and %s', $min, $max));
    } elseif (isset($min) && $value < $min) {
      throw new ValidateException(sprintf('Must be equal or greater than %s', $min));
    } elseif (isset($max) && $value > $max) {
      throw new ValidateException(sprintf('Must be equal or less than %s', $max));
    }

    return $value;
  }
}
