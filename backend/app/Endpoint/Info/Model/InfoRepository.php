<?php

namespace App\Endpoint\Info\Model;

use App\Core\Model\Endpoint;
use App\Core\Model\Repository;
use function count;
use function explode;

final class InfoRepository extends Repository
{
  public function todayRecipesCount(): int
  {
    $result = $this->request(Endpoint::Quotes);

    return count(explode(' ', $result['quote']));
  }


  public function todayCommentsCount(): int
  {
    $result = $this->request(Endpoint::Quotes);

    return count(explode(' ', $result['quote']));
  }


  /**
   * @return RecipeFeedItem[]
   */
  public function findFeedRecipes(): array
  {
    $result = $this->request(Endpoint::Recipes, params: [
      'limit' => 5,
      'select' => 'name,image',
    ]);

    return $this->factory->createCollection(RecipeFeedItem::class, $result['recipes']);
  }
}