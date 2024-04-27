<?php

namespace App\Endpoint\Admin\Recipe\Model;

use App\Core\Model\Entity;

class Comment extends Entity
{
  public string $body;
  public CommentUser $user;
}