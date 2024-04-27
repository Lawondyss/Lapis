<?php

namespace Lapis\Security\JWT;

interface SecretKeyProvider
{
  public function getSecretKey(): string;
}