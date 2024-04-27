<?php

namespace Lapis\Resource;

use Lapis\Exception\MemberAccess;

final class Input extends Resource
{
  public function __set(string $name, $value): void
  {
    throw new MemberAccess('Cannot set input resource');
  }


  public function __unset(string $name): void
  {
    throw new MemberAccess('Cannot unset input resource');
  }


  public function offsetSet(mixed $offset, mixed $value): void
  {
    throw new MemberAccess('Cannot set input resource');
  }


  public function offsetUnset(mixed $offset): void
  {
    throw new MemberAccess('Cannot unset input resource');
  }
}
