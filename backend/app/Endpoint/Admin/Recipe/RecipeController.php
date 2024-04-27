<?php

namespace App\Endpoint\Admin\Recipe;

use App\Endpoint\Admin\Recipe\Model\RecipeRepository;
use App\Endpoint\Admin\SecureController;

final class RecipeController extends SecureController
{
  public function __construct(
    private readonly RecipeRepository $repository,
  ) {}


  public function actionGET(): void
  {
    if (isset($this->id)) {
      $recipe = $this->repository->getRecipe($this->id);
      $this->output->setObject($recipe->toArray());
    } else {
      $recipes = $this->repository->findRecipes();
      $this->output->setCollection($recipes);
    }
  }
}