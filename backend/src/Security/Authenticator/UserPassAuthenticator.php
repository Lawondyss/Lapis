<?php

namespace Lapis\Security\Authenticator;

use Lapis\Exception\InvalidAuthentication;
use Lapis\Security\JWT\Token;
use Nette\Http\Request;
use Override;

class UserPassAuthenticator extends VerifyAuthenticator
{
  /**
   * @throws InvalidAuthentication
   */
  #[Override]
  public function authenticate(Request $request): Token
  {
    return $this->tokenManager->findByUserPass(
      $request->getPost('user'),
      $request->getPost('pass'),
    ) ?? throw new InvalidAuthentication('Incorrect login credentials');
  }
}