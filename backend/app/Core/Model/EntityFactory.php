<?php

namespace App\Core\Model;

use Nette\Utils\Strings;
use ReflectionClass;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use function array_map;
use function array_search;
use function class_exists;
use function in_array;
use function is_array;
use function str_ends_with;
use function substr;

final class EntityFactory
{
  /**
   * @template T of Entity
   * @param class-string<T> $class
   * @param array<string, mixed>[] $data
   * @return Entity<T>[]
   */
  public function createCollection(string $class, array $data): array
  {
    return array_map(fn(array $d) => $this->createEntity($class, $d), $data);
  }


  /**
   * @template T of Entity
   * @param class-string<T> $class
   * @param array<string, mixed> $data
   * @return Entity<T>
   */
  public function createEntity(string $class, array $data): Entity
  {
    $reflection = new ReflectionClass($class);
    $entity = new $class;

    foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
      $name = $property->getName();

      $value = $data[$name] ?? throw new ModelException("Not found data for '{$class}::{$name}'");

      $entity->{$name} = $this->retype($property, $value);
    }

    return $entity;
  }


  private function getAllTypes(ReflectionProperty $reflection): array
  {
    $type = $reflection->getType();
    $types = $type instanceof ReflectionUnionType
      ? $type->getTypes()
      : [$type];

    $output = array_map(static fn(ReflectionType $type) => $type->getName(), $types);

    $comment = $reflection->getDocComment();

    if ($comment) {
      $matches = Strings::match($comment, '~@var\s+(?<type>\S+)\s~m');
      $docType = $matches['type'] ?? false;

      if ($docType && in_array('array', $output, strict: true)) {
        $key = array_search('array', $output, strict: true);
        unset($output[$key]);
        $output[] = $docType;
      }
    }

    return $output;
  }


  private function retype(ReflectionProperty $reflection, mixed $value): mixed
  {
    $type = $reflection->getType()->getName();

    if (str_ends_with($type, '[]')) {
      is_array($value) || throw new ModelException("Data must be an array");
      $type = substr($type, 0, -2);
    }

    return $this->doRetype($type, $value);
  }


  private function doRetype(string $type, mixed $value): mixed
  {
    if (is_array($value)) {
      return array_map(fn(mixed $v) => $this->doRetype($type, $v), $value);
    }

    return class_exists($type)
      ? $this->createEntity($type, $value)
      : $value;
  }
}