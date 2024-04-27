<?php

namespace Lapis\Security\JWT;

use Firebase\JWT\JWT;
use function strtotime;

final readonly class Generator
{
  public function __construct(
    private SecretKeyProvider $secretKeyProvider,
  ) {}


  public function createWithSecretKey(array $userData, string $expire = '+1 hour'): Token
  {
    $expiration = strtotime($expire);

    $raw = JWT::encode([
      'exp' => $expiration,
      'data' => $userData,
    ], $this->secretKeyProvider->getSecretKey(), 'HS256');

    return new Token($raw, $expiration, $userData);
  }
}