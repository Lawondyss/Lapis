<?php

namespace App\Core\Security;

use Lapis\Exception\InvalidAuthentication;
use Lapis\Security\Authenticator\UserPassTokenManager;
use Lapis\Security\JWT\Generator;
use Lapis\Security\JWT\Token;
use Lapis\Security\JWT\Validator;
use Nette\Utils\Strings;
use Override;

final class TokenManager implements UserPassTokenManager
{
  private const array Users = [
    'doktor' => [6, '1234', 'admin'],
    'princezna' => [10, 'abcd', 'user'],
  ];


  public function __construct(
    private readonly Validator $jwtValidator,
    private readonly Generator $jwtGenerator,
  ) {}


  /**
   * @throws InvalidAuthentication
   */
  #[Override]
  public function verify(string $token): Token
  {
    return $this->jwtValidator->checkWithSecretKey($token);
  }


  #[Override]
  public function refresh(Token $token): Token
  {
    return $this->jwtGenerator->createWithSecretKey($token->payload);
  }


  #[Override]
  public function findByUserPass(string $user, string $password): ?Token
  {
    $user = Strings::lower($user);

    [$id, $pass, $roles] = self::Users[$user] ?? [null, null, null];

    return $password === $pass
      ? $this->jwtGenerator->createWithSecretKey([
        'id' => $id,
        'name' => Strings::firstUpper($user),
        'roles' => (array)$roles,
      ])
      : null;
  }
}