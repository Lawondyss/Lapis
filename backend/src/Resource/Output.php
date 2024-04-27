<?php

namespace Lapis\Resource;

use Lapis\Exception\MemberAccess;
use function array_is_list;

final class Output extends Resource
{
  private OutputType $type = OutputType::Data;


  public function type(): OutputType
  {
    return $this->type;
  }


  public function offsetExists(mixed $offset): bool
  {
    throw new MemberAccess('Array access can be use only as incrementally adds (Output[] = ...)');
  }


  public function offsetGet(mixed $offset): mixed
  {
    throw new MemberAccess('Array access can be use only as incrementally adds (Output[] = ...)');
  }


  public function offsetSet(mixed $offset, mixed $value): void
  {
    if (isset($offset)) {
      throw new MemberAccess('Array access can be use only as incrementally adds (Output[] = ...)');
    }

    if (!array_is_list($this->data)) {
      throw new MemberAccess('For incrementally adds must be Output a list');
    }

    parent::offsetSet($offset, $value);
  }


  public function offsetUnset(mixed $offset): void
  {
    throw new MemberAccess('Array access can be use only as incrementally adds (Output[] = ...)');
  }


  public function setObject(iterable $object): void
  {
    foreach ($object as $key => $value) {
      $this->{$key} = $value;
    }
  }


  public function setCollection(iterable $collection): void
  {
    foreach ($collection as $object) {
      $this[] = $object;
    }
  }


  public function setFile(ResponseFile $file): void
  {
    $this->type = OutputType::File;
    $this->file = $file;
  }
}
