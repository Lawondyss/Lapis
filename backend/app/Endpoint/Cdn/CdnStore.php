<?php

namespace App\Endpoint\Cdn;

use App\Core\Model\Endpoint;
use App\Core\Model\Repository;

final class CdnStore extends Repository
{
  public function findImage(int $id): ?Image
  {
    $data = $this->request(Endpoint::Recipes, $id, ['select' => 'name,image']);

    return isset($data)
      ? new Image($data['name'], $data['image'])
      : null;
  }
}