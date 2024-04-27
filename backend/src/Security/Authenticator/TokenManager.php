<?php

namespace Lapis\Security\Authenticator;

use Lapis\Exception\InvalidAuthentication;
use Lapis\Security\JWT\Token;

interface TokenManager
{
  /**
   * @throws InvalidAuthentication
   */
  public function verify(string $token): Token;


  public function refresh(Token $token): Token;
}