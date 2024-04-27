<?php

namespace App\Endpoint\Admin\Recipe\Model;

class RecipeDetail extends RecipeListItem
{
  /** @var string[] */
  public array $ingredients;
  /** @var string[] */
  public array $instructions;
  public int $prepTimeMinutes;
  public int $cookTimeMinutes;
  public int $caloriesPerServing;
  public int $servings;
}