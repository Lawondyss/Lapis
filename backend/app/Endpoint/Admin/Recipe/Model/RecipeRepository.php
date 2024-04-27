<?php

namespace App\Endpoint\Admin\Recipe\Model;

use App\Core\Model\Endpoint;
use App\Core\Model\Repository;
use function sleep;

final class RecipeRepository extends Repository
{
  /**
   * @return RecipeListItem[]
   */
  public function findRecipes(): array
  {
    sleep(1); // simulate a lag
    $data = $this->request(Endpoint::Recipes, params: [
      'limit' => 10,
      'select' => 'id,name,difficulty,tags,image',
    ]);

    return $this->factory->createCollection(RecipeListItem::class, $data['recipes']);
  }


  public function getRecipe(int $id): RecipeDetail
  {
    $data = $this->request(Endpoint::Recipes, $id, [
      'select' => 'id,name,difficulty,tags,image,ingredients,instructions,prepTimeMinutes,cookTimeMinutes,caloriesPerServing,servings',
    ]);

    return $this->factory->createEntity(RecipeDetail::class, $data);
  }


  /**
   * @param int $recipeId
   * @return Comment[]
   */
  public function findComments(int $recipeId): array
  {
    sleep(2); // simulate a lag
    $data = $this->request(Endpoint::Comments, $recipeId, [
      'limit' => 5,
      'select' => 'id,body,user',
    ]);

    return $this->factory->createCollection(Comment::class, $data['comments']);
  }
}