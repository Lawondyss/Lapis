<?php

namespace Lapis\App;

use Lapis\Resource\Input;
use Lapis\Resource\Output;
use Lapis\Security\User;
use Nette\Http\Request;
use Nette\Http\Response;

abstract class Controller
{
  public string $name;

  public ?int $apiVersion;

  public int|string|null $id;

  protected Request $request;

  protected Response $response;

  protected User $user;

  protected Input $input;

  protected Output $output;


  public function __invoke(): Output
  {
    return $this->output;
  }


  final public function injectServices(
    Request $request,
    Response $response,
    User $user,
    Input $input,
    Output $output,
  ): void {
    $this->request = $request;
    $this->response = $response;
    $this->user = $user;
    $this->input = $input;
    $this->output = $output;
  }


  public function startup(): void {}


  public function shutdown(): void {}
}