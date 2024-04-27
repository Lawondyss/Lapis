<?php

namespace App\Endpoint\Info;

use App\Endpoint\Info\Model\InfoRepository;
use App\Endpoint\NonSecureController;

final class InfoController extends NonSecureController
{
  public function __construct(
    private readonly InfoRepository $repository,
  ) {}


  public function actionTodayNewCountsGET(): void
  {
    $this->output->recipes = $this->repository->todayRecipesCount();
    $this->output->comments = $this->repository->todayCommentsCount();
  }


  public function actionNewRecipesGET(): void
  {
    $recipes = $this->repository->findFeedRecipes();

    foreach ($recipes as $recipe) {
      $this->output[] = $recipe->toArray();
    }
  }
}