<?php

namespace App\Endpoint\Auth;

use Lapis\Utils\Schema\Expect;
use Lapis\Utils\Schema\Schema;
use App\Endpoint\NonSecureController;

final class AuthController extends NonSecureController
{
  public function schemaPOST(): Schema
  {
    return Expect::structure([
      'user' => Expect::string()->required(),
      'pass' => Expect::string()->required(),
    ]);
  }


  public function actionPOST(): void
  {
    $this->user->login();
    $this->sendToken($this->user->token());
  }
}