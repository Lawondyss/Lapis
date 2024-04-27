<?php

namespace Lapis\Resource;

use Lapis\Exception\InvalidState;
use Lapis\Utils\Notation\Notation;
use Lapis\Utils\Schema\Exception\ValidateException;
use Lapis\Utils\Schema\Exception\ValidationException;
use Lapis\Utils\Schema\Processor;
use Lapis\Utils\Schema\Schema;
use Nette\Http\IResponse;
use Nette\Http\Request;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nette\Utils\Strings;
use function array_merge;
use function str_starts_with;

class ResourceFactory
{
  public function createOutput(): Output
  {
    return new Output;
  }


  /**
   * @throws JsonException|ValidateException|ValidationException|InvalidState
   */
  public function createInput(Request $request, ?Schema $schema = null): Input
  {
    $data = $this->createRequestData($request);

    if (isset($schema)) {
      $data = Processor::process($data, $schema);
    }

    return Input::from($data);
  }


  public function createResponseErrorData(int $code, ?string $message = null, ?array $errors = null): ResponseData
  {
    $message ??= IResponse::ReasonPhrases[$code] ?? null;

    return $this->createResponseData('ERROR', $code, message: $message, errors: $errors);
  }


  public function createResponseSuccessData(int $code, Output $output): ResponseData|ResponseFile
  {
    return match ($output->type()) {
      OutputType::Data => $this->createResponseData('OK', $code, output: $output),
      OutputType::File => $output->file,
    };
  }


  /**
   * @throws JsonException
   */
  protected function createRequestData(Request $request): array
  {
    $headerData = [];
    foreach ($request->headers as $name => $value) {
      $name = Notation::asKebabCase($name);

      if (str_starts_with($name, 'x-data-')) {
        $name = Notation::asCamelCase(Strings::after($name, 'x-data-'));
        $headerData[$name] = $value;
      }
    }

    $bodyData = $request->rawBody && $request->getHeader('Content-Type') === 'application/json'
      ? Json::decode($request->rawBody, forceArrays: true)
      : [];

    return array_merge($headerData, $bodyData, $request->post, $request->query);
  }


  protected function createResponseData(
    string $status,
    int $code,
    ?string $message = null,
    ?Output $output = null,
    ?array $errors = null,
  ): ResponseData {
    $data = [
      'status' => $status,
      'code' => $code,
    ];

    if (isset($message)) {
      $data['message'] = $message;
    }

    if (isset($output)) {
      $data['result'] = $output->toArray() ?: null;
    }

    if (isset($errors)) {
      $data['errors'] = $errors;
    }

    return ResponseData::from($data);
  }
}
