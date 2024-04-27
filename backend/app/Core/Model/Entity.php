<?php

namespace App\Core\Model;

abstract class Entity
{
  public function toArray(): array
  {
    $arr = [];

    foreach (get_object_vars($this) as $key => $value) {
      if ($value instanceof self) {
        $value = $value->toArray();
      }

      $arr[$key] = $value;
    }

    return $arr;
  }
}