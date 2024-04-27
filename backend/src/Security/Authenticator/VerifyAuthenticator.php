<?php

namespace Lapis\Security\Authenticator;

use Lapis\Exception\InvalidAuthentication;
use Lapis\Security\Authenticator;
use Lapis\Security\JWT\Token;
use Nette\Http\Request;
use Override;
use function explode;
use function time;

abstract class VerifyAuthenticator implements Authenticator
{
  /** Time window in seconds during which the JWT token is refreshed */
  public const int TokenRefreshTime = 60 * 5;


  public function __construct(
    protected readonly TokenManager $tokenManager,
  ) {}


  /**
   * @throws InvalidAuthentication
   */
  #[Override]
  public function verify(Request $request): Token
  {
    $auth = $request->getHeader('Authorization');

    if ($auth === null) {
      throw new InvalidAuthentication('Missing authentication header');
    }

    [$type, $token] = explode(' ', $auth);

    if ($type !== 'Bearer') {
      throw new InvalidAuthentication('Wrong type of authentication');
    }

    $token = $this->tokenManager->verify($token);

    return ($token->expiration - time()) < self::TokenRefreshTime
      ? $this->tokenManager->refresh($token)
      : $token;
  }
}