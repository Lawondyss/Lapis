<?php

namespace App\Endpoint;

use Lapis\App\Controller;
use Lapis\Security\Authenticator;
use Lapis\Security\JWT\Token;

abstract class NonSecureController extends Controller
{
  protected function setTokenHeader(Token $token): void
  {
    $this->response->setHeader(Authenticator::TokenHeader, $token->raw);
  }


  protected function sendToken(Token $token): void
  {
    $this->response->setCode($this->response::S204_NoContent);
    $this->setTokenHeader($token);
  }
}