<?php

namespace App\Endpoint\Cdn;

use Nette\Utils\Strings;

final class Image
{
  public function __construct(
    public readonly string $name,
    public readonly string $url,
  ) {}


  public function fileName(): string
  {
    $suffix = Strings::after($this->url, '.', -1);

    return "{$this->name}.{$suffix}";
  }
}