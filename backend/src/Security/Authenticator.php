<?php

namespace Lapis\Security;

use Lapis\Exception\InvalidAuthentication;
use Lapis\Security\JWT\Token;
use Nette\Http\Request;

interface Authenticator
{
  public const string TokenHeader = 'X-Auth-Token';


  /**
   * @throws InvalidAuthentication
   */
  public function authenticate(Request $request): Token;


  /**
   * @throws InvalidAuthentication
   */
  public function verify(Request $request): Token;
}