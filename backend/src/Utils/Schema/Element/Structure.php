<?php

namespace Lapis\Utils\Schema\Element;

use Lapis\Utils\Schema\Exception\ValidateException;
use Lapis\Utils\Schema\Exception\ValidationException;
use Lapis\Utils\Schema\Schema;
use function is_array;
use function is_object;

class Structure extends Type
{
  public function __construct(
    /** @var array<string, Schema> */
    private readonly array $items,
  ) {
    (function(Schema ...$items) {})(...array_values($this->items));

    parent::__construct();
  }

  public function default(): array
  {
    $defaults = [];

    foreach ($this->items as $name => $schema) {
      $defaults[$name] = $schema->default();
    }

    return $defaults;
  }


  /**
   * @return array<string, mixed>
   * @throws ValidateException|ValidationException
   */
  public function normalize(mixed $value): array
  {
    $value = parent::normalize($value);

    if (is_object($value)) {
      $value = (array)$value;
    }

    if (!is_array($value)) {
      throw new ValidateException('Must be array');
    }

    $errors = [];
    $output = [];
    $keys = [];

    foreach ($value as $key => $val) {
      $keys[] = $key;

      $item = $this->items[$key] ?? null;

      if (!isset($item)) {
        $errors[$key] = 'Unexpected field';
        continue;
      }

      try {
        $output[$key] = $item->normalize($val);
      } catch (ValidateException $e) {
        $errors[$key] = $e->getMessage();
      }
    }

    $missing = $this->checkRequired($keys);

    foreach ($missing as $field) {
      $errors[$field] = 'Field is required';
    }

    if ($errors !== []) {
      throw new ValidationException($errors);
    }

    return $output;
  }


  private function checkRequired(array $usedKeys): array
  {
    $missing = [];

    foreach ($this->items as $key => $item) {
      if ($item->required && !in_array($key, $usedKeys, true)) {
        $missing[] = $key;
      }
    }

    return $missing;
  }
}
