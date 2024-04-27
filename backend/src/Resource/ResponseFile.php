<?php

namespace Lapis\Resource;

use Closure;
use function fputcsv;
use function readfile;

class ResponseFile
{
  /**
   * @param Closure $renderer Create output after sent headers
   */
  public function __construct(
    public readonly string $outputName,
    private readonly Closure $renderer,
  ) {}


  public function render(): void
  {
    ($this->renderer)();
  }


  public static function fromFile(string $filepath, string $name): static
  {
    return new static(
      $name,
      static fn() => readfile($filepath)
    );
  }


  public static function asCsv(
    iterable $data,
    string $name,
    string $separator = ',',
    string $enclosure = '"',
    string $escape = '\\',
    string $eol = PHP_EOL,
  ): static {
    return new static(
      $name,
      function () use ($data, $separator, $enclosure, $escape, $eol) {
        $stream = fopen('php://output', 'w');

        foreach ($data as $row) {
          echo fputcsv($stream, $row, $separator, $enclosure, $escape, $eol);
        }

        fclose($stream);
      }
    );
  }
}