<?php

namespace Lapis\Security\Authenticator;

use Lapis\Security\JWT\Token;

interface UserPassTokenManager extends TokenManager
{
  public function findByUserPass(string $user, string $password): ?Token;
}