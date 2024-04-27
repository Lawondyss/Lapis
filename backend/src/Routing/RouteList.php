<?php

namespace Lapis\Routing;

use Lapis\Utils\Notation\Notation;
use Nette\Http\IRequest;
use Nette\Routing\RouteList as NetteRouteList;
use Nette\Utils\Strings;
use function explode;
use function is_numeric;
use function sprintf;
use function str_contains;

class RouteList extends NetteRouteList
{
  public const string VersionKey = 'version';
  public const string ModuleKey = 'module';
  public const string ControllerKey = 'controller';
  public const string ActionKey = 'action';
  public const string IdKey = 'id';

  private const array DefaultParams = [
    self::VersionKey => null,
    self::ModuleKey => null,
    self::ControllerKey => null,
    self::ActionKey => null,
    self::IdKey => null,
  ];

  public ?int $defaultVersion = null;


  public function __construct(
    private ?string $module = null,
  ) {
    parent::__construct();
  }


  public function withModule(string $module): static
  {
    $self = new static;
    $self->module = $module;
    $self->parent = $this;
    $this->add($self);

    return $self;
  }


  public function addRoute(string $mask, array $metadata = [], int $oneWay = 0): RouteList
  {
    // mask defined as "Controller:[Action|+|?][:]", second ":" for adding mask of optional ID
    if (str_contains($mask, ':')) {
      [$controller, $action, $optionalId] = [...explode(':', $mask), null];

      $action = match ($action) {
        '+' => sprintf('/<%s [a-z-]+>', self::ActionKey), // adding mask for required action
        '?' => sprintf('[/<%s [a-z-]+>]', self::ActionKey), // adding mask for optional action
        default => $action,
      };

      $id = isset($optionalId)
        ? sprintf('[/<%s>]', self::IdKey)
        : '';

      // action can be a mask instead of an action name
      $isName = !!Strings::match($action, '~^[a-zA-Z]+$~');

      $metadata = [
        self::ControllerKey => $controller,
        self::ActionKey => $isName ? $action : null,
        self::IdKey => null,
        ...$metadata,
      ];

      /* Creates a mask starts with casing controller name */
      $mask = Notation::asKebabCase($controller);

      // adds casing action name or mask in action
      $mask .= $isName
        ? Notation::asKebabCase($action)
        : $action;

      // adds possibly a mask of ID
      $mask .= $id;
    }

    // add module prefix of mask
    if (isset($this->module)) {
      $mask = Notation::asKebabCase($this->module) . "/{$mask}";
    }

    // add default prefix of mask
    $mask = '/api/' . (isset($this->defaultVersion) ? "v<version={$this->defaultVersion} \d+>/" : '') . $mask;

    $metadata = [
      self::VersionKey => $this->defaultVersion,
      self::ModuleKey => $this->module,
      ...$metadata,
    ];

    return parent::addRoute(trim($mask), $metadata, $oneWay);
  }


  public function match(IRequest $httpRequest): ?array
  {
    $params = parent::match($httpRequest);

    if ($params !== null) {
      $params[self::VersionKey] = is_numeric($params[self::VersionKey] ?? null)
        ? (int)$params[self::VersionKey]
        : $this->defaultVersion;

      $params[self::ModuleKey] = isset($params[self::ModuleKey])
        ? Notation::asPascalCase($params[self::ModuleKey])
        : null;

      $params[self::ControllerKey] = isset($params[self::ControllerKey])
        ? Notation::asPascalCase($params[self::ControllerKey])
        : null;

      $params[self::ActionKey] = isset($params[self::ActionKey])
        ? Notation::asPascalCase($params[self::ActionKey])
        : null;

      $params[self::IdKey] = is_numeric($params[self::IdKey])
        ? (int)$params[self::IdKey]
        : null;

      $params = [...self::DefaultParams, ...$params];
    }

    return $params;
  }
}