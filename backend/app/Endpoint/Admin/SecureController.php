<?php

namespace App\Endpoint\Admin;

use App\Endpoint\NonSecureController;

abstract class SecureController extends NonSecureController
{
  public function startup(): void
  {
    parent::startup();

    $this->user->verify();
    $this->setTokenHeader($this->user->token());
  }
}