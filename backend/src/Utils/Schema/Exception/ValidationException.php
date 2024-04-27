<?php

namespace Lapis\Utils\Schema\Exception;

use Exception;

class ValidationException extends Exception
{
  public function __construct(
    private readonly array $errors,
  ) {
    parent::__construct();
  }


  public function getErrors(): array
  {
    return $this->errors;
  }
}
