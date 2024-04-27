<?php

namespace App\Endpoint\Admin\Recipe\Model;

use App\Core\Model\Entity;

class RecipeListItem extends Entity
{
  public int $id;
  public string $name;
  public string $difficulty;
  /** @var string[] */
  public array $tags;
  public string $image;
}