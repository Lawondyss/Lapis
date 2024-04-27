<?php

namespace Lapis\Security;

interface Authorizator
{
  public function isAuthorize(User $user, mixed $permission = null): bool;
}