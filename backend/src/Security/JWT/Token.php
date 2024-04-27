<?php

namespace Lapis\Security\JWT;

use Lapis\Exception\MemberAccess;

readonly class Token
{
  public function __construct(
    public string $raw,
    public ?int $expiration,
    public array $payload,
  ) {}


  /**
   * @throws MemberAccess
   */
  public function __get(string $name): mixed
  {
    return $this->get($name);
  }


  /**
   * @throws MemberAccess
   */
  public function __set(string $name, $value): void
  {
    throw new MemberAccess("Payload for '{$name}' is readonly");
  }


  public function __isset(string $name): bool
  {
    return isset($this->payload[$name]);
  }


  /**
   * @throws MemberAccess
   */
  public function __unset(string $name): void
  {
    throw new MemberAccess("Payload for '{$name}' is readonly");
  }


  /**
   * @throws MemberAccess
   */
  public function get(string $name): mixed
  {
    return $this->payload[$name] ?? throw new MemberAccess("Payload for '{$name}' not exists");
  }
}