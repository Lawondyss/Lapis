<?php

namespace Lapis\Security;

use Lapis\Exception\InvalidAuthentication;
use Lapis\Security\JWT\Token;
use Nette\Http\Request;

class User
{
  private ?Token $token = null;


  public function __construct(
    private readonly Authenticator $authenticator,
    private readonly Authorizator $authorizator,
    private readonly Request $request,
  ) {}


  public function id(): string|int|null
  {
    return $this->token?->id;
  }


  public function roles(): array
  {
    return $this->token?->roles ?? [];
  }


  public function token(): ?Token
  {
    return $this->token;
  }


  /**
   * @throws InvalidAuthentication
   */
  public function verify(): void
  {
    if (isset($this->token)) {
      return;
    }

    $this->token = $this->authenticator->verify($this->request);
  }


  /**
   * @throws InvalidAuthentication
   */
  public function login(): Token
  {
    return $this->token = $this->authenticator->authenticate($this->request);
  }


  public function isAllowed(?string $role = null): bool
  {
    return $this->authorizator->isAuthorize($this, $role);
  }
}