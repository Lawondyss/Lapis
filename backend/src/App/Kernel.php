<?php

namespace Lapis\App;

use Lapis\DI\ControllersExtension;
use Lapis\Exception\BadRequest;
use Lapis\Exception\InvalidAuthentication;
use Lapis\Exception\InvalidState;
use Lapis\Resource\ResourceFactory;
use Lapis\Resource\ResponseData;
use Lapis\Resource\ResponseFile;
use Lapis\Routing\RouteList;
use Lapis\Security\Authenticator;
use Lapis\Security\User;
use Lapis\Utils\DotEnv\DotEnv;
use Lapis\Utils\Notation\Notation;
use Lapis\Utils\Schema\Exception\ValidateException;
use Lapis\Utils\Schema\Exception\ValidationException;
use Lapis\Utils\Schema\Schema;
use Nette\DI\Container;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Routing\Router;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Throwable;
use Tracy\ILogger;
use function gmdate;
use function in_array;
use function lcfirst;
use function method_exists;
use function sprintf;
use function str_replace;
use function ucfirst;

final readonly class Kernel
{
  private const int EmptyResponse = 0;


  public function __construct(
    private bool $isDebugMode,
    private Container $dic,
    private Router $router,
    private Request $request,
    private Response $response,
    private DotEnv $env,
    private User $user,
    private ResourceFactory $factory,
    private ILogger $logger,
  ) {}


  public function run(): void
  {
    // Nothing is returned for CORS preflight check
    if ($this->initCors()) return;

    try {
      $params = $this->router->match($this->request);
      [
        RouteList::ModuleKey => $moduleName,
        RouteList::ControllerKey => $controllerName,
        RouteList::ActionKey => $actionName,
        RouteList::IdKey => $id,
        RouteList::VersionKey => $version,
      ] = $params ?? throw new BadRequest('No route for HTTP request');

      $controller = $this->getController($controllerName, $moduleName);
      $controller->apiVersion = $version;
      $controller->id = $id;

      $schemaProvider = $this->findSchema($controller, $actionName);
      $controller->injectServices(
        request: $this->request,
        response: $this->response,
        user: $this->user,
        input: $this->factory->createInput($this->request, $schemaProvider),
        output: $this->factory->createOutput(),
      );

      $controller->startup();
      $this->callAction($controller, $actionName);
      $controller->shutdown();

      $httpCode = $this->response->getCode();
      $isBetween = static fn(int $min, int $max) => $min <= $httpCode && $httpCode <= $max;
      $responseData = match (true) {
        $httpCode === 204, $isBetween(300, 399) => self::EmptyResponse,
        $isBetween(200, 299) => $this->factory->createResponseSuccessData($httpCode, $controller()),
        default => $this->factory->createResponseErrorData($httpCode)
      };

    } catch (JsonException $exc) {
      $responseData = $this->factory->createResponseErrorData(
        $this->response::S400_BadRequest, 'JSON in body of request is invalid'
      );
      $this->logger->log($exc, $this->logger::EXCEPTION);

    } catch (ValidationException|ValidateException $exc) {
      $errors = $exc instanceof ValidationException
        ? $exc->getErrors()
        : [$exc->getMessage()];
      $responseData = $this->factory->createResponseErrorData(
        $this->response::S422_UnprocessableEntity, 'Invalid input, see errors', $errors
      );

    } catch (InvalidAuthentication $exc) {
      $this->logger->log($exc, $this->logger::WARNING);
      $responseData = $this->factory->createResponseErrorData(
        $this->response::S401_Unauthorized, $exc->getMessage()
      );

    } catch (BadRequest $exc) {
      $this->isDebugMode && throw $exc;
      $this->logger->log($exc, $this->logger::WARNING);
      $responseData = $this->factory->createResponseErrorData($this->response::S404_NotFound);

    } catch (Throwable $exc) {
      $this->isDebugMode && throw $exc;
      $this->logger->log($exc, $this->logger::EXCEPTION);
      $responseData = $this->factory->createResponseErrorData($this->response::S500_InternalServerError);
    }

    match (true) {
      $responseData instanceof ResponseData => $this->sendData($responseData),
      $responseData instanceof ResponseFile => $this->sendFile($responseData),
    };
  }


  /**
   * @return bool Is preflight request
   */
  private function initCors(): bool
  {
    // HTTP method OPTIONS is preflight check of CORS
    $isPreflight = $this->request->method === $this->request::Options;

    [$allowOrigin, $allowHeaders] = $isPreflight
      ? [$this->request->getHeader('Origin'), $this->request->getHeader('Access-Control-Request-Headers')]
      : [$this->env->CORS_ALLOW_ORIGIN, '*'];

    $this->response
      ->addHeader('Access-Control-Allow-Credentials', 'true')
      ->addHeader('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS')
      ->addHeader('Access-Control-Allow-Origin', $allowOrigin)
      ->addHeader('Access-Control-Allow-Headers', $allowHeaders)
      ->addHeader('Access-Control-Expose-Headers', Authenticator::TokenHeader);

    return $isPreflight;
  }


  /**
   * @throws InvalidState
   * @throws BadRequest
   */
  private function getController(?string $controller, ?string $module): Controller
  {
      $controller ?? throw new InvalidState('Missing definition for controller name');

    $module = $module ? ucfirst($module) : null;
    $controller = ucfirst($controller);
    $serviceName = ControllersExtension::Prefix;

    if (isset($module)) {
      $serviceName .= lcfirst($module) . '.';
    }

    $serviceName .= lcfirst($controller);

    if (!$this->dic->hasService($serviceName)) {
      $name = 'App\\Endpoint\\';

      if (isset($module)) {
        $name .= "{$module}\\";
      }

      $name .= "{$controller}\\{$controller}Controller";

      throw new BadRequest("Missing controller '{$name}'");
    }

    $controller = $this->dic->getService($serviceName);
    $controller->name = Notation::asPascalCase(str_replace(ControllersExtension::Prefix, '', $serviceName));

    return $controller;
  }


  /**
   * Finds method providing Schema
   * Format for method: schema[PascalCaseAction][METHOD]
   * Prioritizes more specific
   */
  private function findSchema(Controller $controller, ?string $name): ?Schema
  {
    foreach ($this->possibleMethods('schema', $name) as $method) {
      if (method_exists($controller, $method)) {
        return $controller->{$method}();
      }
    }

    return null;
  }


  /**
   * Finds and calls method processing request
   * Format for method: action[PascalCaseAction][METHOD]
   * Prioritizes more specific
   *
   * @throws BadRequest
   */
  private function callAction(Controller $controller, ?string $name): void
  {
    foreach ($this->possibleMethods('action', $name) as $method) {
      if (method_exists($controller, $method)) {
        $controller->{$method}();

        return;
      }
    }

    $name ??= $this->request->method;
    throw new BadRequest("Missing action for '{$name}' in {$controller->name} controller");
  }


  /**
   * Generates possible names of controller's methods
   *
   * @return string[]
   */
  private function possibleMethods(string $prefix, ?string $name): array
  {
    $httpMethod = $this->request->method;

    return isset($name)
      ? [
        sprintf('%s%s%s', $prefix, $name, $httpMethod),
        sprintf('%s%s', $prefix, $name),
      ] : [
        sprintf('%s%s', $prefix, $httpMethod),
        $prefix,
      ];
  }


  private function sendData(ResponseData $responseData): void
  {
    if ($this->response->isSent()) {
      $this->isDebugMode && dump($responseData, $responseData->toArray());

    } else {
      try {
        $this->response->setCode($responseData->code);

        if (in_array($this->request->method, [$this->request::Head, $this->request::Options])) {
          isset($responseData->message) && $this->response->setHeader('X-Message', $responseData->message);
          isset($responseData->error) && $this->response->setHeader('X-Error', $responseData->error);
          isset($responseData->errors) && $this->response->setHeader('X-Errors', Json::encode($responseData->errors->toArray()));
          isset($responseData->result) && $this->response->setHeader('X-Result', Json::encode($responseData->result->toArray()));
        } else {
          $this->response->setContentType('application/json');
          echo Json::encode($responseData->toArray());
        }

      } catch (JsonException $exc) {
        $this->isDebugMode && throw $exc;
        $this->logger->log($exc, $this->logger::EXCEPTION);
        $this->response->setCode($this->response::S500_InternalServerError);
      }
    }
  }


  private function sendFile(ResponseFile $responseFile): void
  {
    $now = gmdate('D, d M Y H:i:s');

    // disabled caching
    $this->response->setHeader('Expires', "{$now} GMT");
    $this->response->setHeader('Cache-Control', 'max-age=0, no-cache, must-revalidate, proxy-revalidate');
    $this->response->setHeader('Last-Modified', "{$now} GMT");

    // force download
    $this->response->setHeader('Content-Type', 'application/force-download');
    $this->response->setHeader('Content-Type', 'application/octet-stream');
    $this->response->setHeader('Content-Type', 'application/download');

    // disposition / encoding on response body
    $this->response->setHeader('Content-Disposition', "attachment;filename={$responseFile->outputName}");
    $this->response->setHeader('Content-Transfer-Encoding', 'binary');

    $responseFile->render();
  }
}