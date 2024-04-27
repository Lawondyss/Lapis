<?php

namespace App\Core\Model;

enum Endpoint: string
{
  case Recipes = '/recipes';
  case Comments = '/comments/post';
  case Quotes = '/quotes/random';
}
