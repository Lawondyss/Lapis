<?php

namespace Lapis\Resource;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Lapis\Exception\MemberAccess;
use Lapis\Utils\Notation\Notation;
use Lapis\Utils\Notation\NotationCase;
use RecursiveArrayIterator;
use function array_key_exists;
use function count;
use function is_iterable;
use function is_numeric;
use function sprintf;

abstract class Resource implements IteratorAggregate, Countable, ArrayAccess
{
  protected array $data = [];


  public static function from(iterable $data): static
  {
    $self = new static;

    foreach ($data as $key => $value) {
      $internal = self::asInternal($key);
      $value = is_iterable($value) ? static::from($value) : $value;
      $self->data[$internal] = $value;
    }

    return $self;
  }


  public function toArray(NotationCase $case = NotationCase::Camel): array
  {
    $arr = [];

    foreach ($this->data as $key => $value) {
      $name = Notation::as($case, $key);
      $arr[$name] = $value instanceof $this ? $value->toArray($case) : $value;
    }

    return $arr;
  }


  public function getIterator(): RecursiveArrayIterator
  {
    return new RecursiveArrayIterator($this->toArray());
  }


  public function count(): int
  {
    return count($this->data);
  }


  public function __get(string $name): mixed
  {
    return $this->get($name);
  }


  public function __set(string $name, mixed $value): void
  {
    $internal = self::asInternal($name);

    $this->data[$internal] = $value;
  }


  public function __isset(string $name): bool
  {
    $internal = self::asInternal($name);

    return isset($this->data[$internal]);
  }


  public function __unset(string $name): void
  {
    $internal = self::asInternal($name);

    unset($this->data[$internal]);
  }


  public function offsetExists(mixed $offset): bool
  {
    $internal = self::asInternal($offset);

    return isset($this->data[$internal]);
  }


  public function offsetGet(mixed $offset): mixed
  {
    return $this->get($offset);
  }


  public function offsetSet(mixed $offset, mixed $value): void
  {
    if (!isset($offset)) {
      $this->data[] = $value;
    } else {
      $internal = self::asInternal($offset);
      $this->data[$internal] = $value;
    }
  }


  public function offsetUnset(mixed $offset): void
  {
    $internal = self::asInternal($offset);

    unset($this->data[$internal]);
  }


  private static function asInternal(string $name): string|int
  {
    return is_numeric($name) ? (int)$name : Notation::asSnakeCase($name);

  }


  private function get(string $requested): mixed
  {
    $internal = self::asInternal($requested);

    if (!array_key_exists($internal, $this->data)) {
      throw new MemberAccess(sprintf('Data for "%s" not exists', $requested));
    }

    return $this->data[$internal];
  }
}
