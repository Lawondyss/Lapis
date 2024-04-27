<?php

namespace Lapis\Security\JWT;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Lapis\Exception\InvalidAuthentication;
use stdClass;
use UnexpectedValueException;

class Validator
{
  public function __construct(
    private readonly SecretKeyProvider $secretKeyProvider,
  ) {}


  /**
   * @throws InvalidAuthentication
   */
  public function checkWithSecretKey(string $token): Token
  {
    try {
      JWT::$leeway = 60 * 5;
      $headers = new stdClass;
      $decoded = JWT::decode(
        $token,
        new Key($this->secretKeyProvider->getSecretKey(), 'HS256'),
        $headers,
      );

      return new Token($token, $decoded->exp, (array)$decoded->data);

    } catch (UnexpectedValueException $exc) {
      throw new InvalidAuthentication($exc->getMessage(), previous: $exc);
    }
  }
}