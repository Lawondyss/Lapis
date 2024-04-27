<?php

namespace App\Endpoint\Cdn;

use Lapis\App\Controller;
use Lapis\Resource\ResponseFile;

final class CdnController extends Controller
{
  public function __construct(
    private readonly CdnStore $store,
  ) {}


  public function actionGET(): void
  {
    $image = $this->store->findImage($this->id);

    $this->output->setFile(ResponseFile::fromFile($image->url, $image->fileName()));
  }
}