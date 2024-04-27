<?php

namespace App\Core\Security;

use Lapis\Security\JWT\SecretKeyProvider;
use Lapis\Utils\DotEnv\DotEnv;
use Override;

final readonly class KeyProvider implements SecretKeyProvider
{
  public function __construct(
    private DotEnv $env,
  ) {}


  #[Override]
  public function getSecretKey(): string
  {
    return $this->env->AUTH_SECRET_KEY;
  }
}