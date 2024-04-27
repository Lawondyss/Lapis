<?php

namespace Lapis\Security\Authorizator;

use Lapis\Security\Authorizator;
use Lapis\Security\User;
use Override;
use function in_array;

class OnlyAdminAuthorizator implements Authorizator
{
  protected const string AdminRole = 'admin';


  #[Override]
  public function isAuthorize(User $user, mixed $permission = null): bool
  {
    return in_array($this::AdminRole, $user->roles(), strict: true);
  }
}