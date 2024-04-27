<?php

namespace Lapis\Utils\DotEnv;

use Lapis\Dirs;
use Nette\Utils\Strings;
use function array_reduce;
use function array_shift;
use function file;
use function implode;
use function is_numeric;
use function is_readable;
use function putenv;
use function realpath;
use function rtrim;
use function str_starts_with;
use function strtolower;
use function trim;
use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

/**
 * @property-read string DEBUG_COOKIE
 * @property-read string AUTH_SECRET_KEY
 * @property-read string CORS_ALLOW_ORIGIN
 */
class DotEnv
{
  private const string NotInterpolateQuote = "'";
  private const array MultilineQuotes = ["'''", '"""'];
  private const string VariableRegex = '[a-zA-Z_]+[a-zA-Z0-9_]*';


  /** @var array<string, string|int|float|null>|null $variables */
  private ?array $variables = null;


  public function __construct(
    private readonly string $directory = Dirs::Root,
    private readonly string $file = '.env',
  ) {
    $this->process();
  }


  /**
   * @throws DotEnvException
   */
  public function __get(string $name): string|int|float|null
  {
    return $this->variables[$name] ?? throw new DotEnvException("Missing variable '{$name}'");
  }


  /**
   * @throws DotEnvException
   */
  public function __set(string $name, mixed $value): never
  {
    throw new DotEnvException("Variable '{$name}' cannot be modified");
  }


  public function __isset(string $name): bool
  {
    return isset($this->variables[$name]);
  }


  /**
   * @throws DotEnvException
   */
  public function __unset(string $name): never
  {
    throw new DotEnvException("Variable '{$name}' cannot be modified");

  }


  /**
   * @throws DotEnvException
   */
  private function process(): void
  {
    $multilineVariable = null;
    $multilineValue = null;
    $interpolate = true;

    $filePath = $this->filePath();

    foreach (file($filePath) as $line) {
      $line = isset($multilineVariable) ? rtrim($line) : trim($line);

      if ($multilineVariable === null && $line === '') {
        continue;
      }

      if (isset($multilineVariable)) {
        $variable = $multilineVariable;
        $value = $line;
      } else {
        // skips line with comment
        if (str_starts_with($line, '#')) {
          continue;
        }

        // Splits the line to variable and value
        $parts = Strings::split($line, '~( *= *)~');
        $variable = array_shift($parts); // first is variable
        array_shift($parts); // second is delimiter
        $value = implode('', $parts); // rest is value

        if (Strings::match($variable, '~^' . self::VariableRegex . '$~') === null) {
          throw new DotEnvException("Wrong format of variable '{$variable}'");
        }
      }

      if ($multilineVariable === null && str_starts_with($value, self::NotInterpolateQuote)) {
        // Detection of quotation marks prohibiting variable substitution in strings
        $interpolate = false;
      }

      $isMultilineQuotes = array_reduce(
        self::MultilineQuotes,
        static fn(bool $result, string $quotes): bool => $result || str_starts_with($value, $quotes),
        false
      );

      if ($isMultilineQuotes) {
        // Detected a multiline
        if (isset($multilineVariable)) {
          // Second occurrence means the end
          $value = implode(PHP_EOL, $multilineValue);
          $multilineVariable = null;
          $multilineValue = null;
        } else {
          // First occurrence means the start
          $multilineVariable = $variable;
          $multilineValue = [];
          continue;
        }

      } elseif (isset($multilineVariable)) {
        // Saves the value for later
        $multilineValue[] = $value;
        continue;

      } else {
        $value = $interpolate ? $this->interpolate($value) : $value;
        $interpolate = true;

        // Removes quotation marks and comment
        $valueMatches = Strings::match($value, '~^([^"\'][^#\s]+)|(["\'])([^\2]*)\2~');
        $value = $valueMatches[3] ?? $valueMatches[1];
      }

      $this->setVariable($variable, $value);
    }
  }


  /**
   * @throws DotEnvException
   */
  private function filePath(): string
  {
    $filePath = $this->directory . DIRECTORY_SEPARATOR . $this->file;
    $realPath = realpath($filePath);

    if ($realPath === false) {
      throw new DotEnvException("Missing config file '{$filePath}'");
    }

    if (!is_readable($realPath)) {
      throw new DotEnvException("Not readable config file '{$filePath}'");
    }

    return $realPath;
  }


  private function interpolate(string $value): string
  {
    return Strings::replace($value, '~\$\{(' . self::VariableRegex . ')}~',
      fn(array $matches): string => $this->variables[$matches[1]] ?? throw new DotEnvException("Missing variable {$matches[1]}"),
    );
  }


  private function setVariable(string $name, string $value): void
  {
    $value = match (true) {
      is_numeric($value) => (int)$value == (float)$value ? (int)$value : (float)$value,
      $value === '' => null,
      in_array(strtolower($value), ['yes', 'true']) => true,
      in_array(strtolower($value), ['no', 'false']) => false,
      default => $value,
    };

    $this->variables[$name] = $value;
    putenv("{$name}={$value}");
    $_ENV[$name] = $value;
  }
}