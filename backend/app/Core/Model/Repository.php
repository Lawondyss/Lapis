<?php

namespace App\Core\Model;

use Nette\Utils\Json;
use function curl_init;
use function curl_setopt_array;
use function http_build_query;
use const CURLOPT_HEADER;

abstract class Repository
{
  private const string Url = 'https://dummyjson.com';


  public function __construct(
    protected readonly EntityFactory $factory,
  ) {}


  protected function request(Endpoint $endpoint, ?int $id = null, array $params = []): ?array
  {
    $url = self::Url . $endpoint->value;

    if (isset($id)) {
      $url .= '/' . $id;
    }

    if ($params !== []) {
      $url .= '?' . http_build_query($params);
    }

    $curl = curl_init($url);
    curl_setopt_array($curl, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER => false,
    ]);

    $result = curl_exec($curl);
    curl_close($curl);

    return $result ? Json::decode($result, forceArrays: true) : null;
  }
}