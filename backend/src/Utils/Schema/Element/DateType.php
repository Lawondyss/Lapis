<?php

namespace Lapis\Utils\Schema\Element;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Lapis\Utils\Schema\Exception\ValidateException;
use Nette\Utils\Strings;
use Nette\Utils\Validators;
use function implode;

class DateType extends Type
{
  private ?DateTimeImmutable $min = null;

  private ?DateTimeImmutable $max = null;


  public function __construct(?DateTimeInterface $default = null)
  {
    parent::__construct($default);
  }


  public function min(DateTimeInterface $min): static
  {
    $this->min = DateTimeImmutable::createFromInterface($min)
                                   ->setTimezone(new DateTimeZone('UTC'))
                                   ->setTime(0, 0);

    return $this;
  }


  public function max(DateTimeInterface $max): static
  {
    $this->max = DateTimeImmutable::createFromInterface($max)
                                   ->setTimezone(new DateTimeZone('UTC'))
                                   ->setTime(23, 59, 59, 999999);

    return $this;
  }


  /**
   * @throws ValidateException
   */
  public function normalize(mixed $value): ?DateTimeImmutable
  {
    $value = parent::normalize($value);

    if (!isset($value)) {
      return null;
    }

    $match = Strings::match($value, '~^\d{4}-[01][0-9]-[0-3][0-9]$~');

    if ($match === null) {
      throw new ValidateException(sprintf('Bad date format for "%s", expect "YYYY-MM-DD"', $value));
    }

    $date = DateTimeImmutable::createFromFormat('Y-m-d', $value, new DateTimeZone('UTC'));

    if ($date === false) {
      $errors = DateTimeImmutable::getLastErrors() ?: ['errors' => []];
      $msg = implode('; ', $errors);
      $msg = $msg !== '' ? " ({$msg})" : '';

      throw new ValidateException(sprintf('Something wrong with date "%s"%s', $value, $msg));
    }

    $date = $date->setTime(0, 0);

    if (isset($this->min) && isset($this->max) && !Validators::isInRange($date, [$this->min, $this->max])) {
      throw new ValidateException(sprintf(
        'Date must be between %s and %s',
        $this->min->format('Y-m-d'), $this->max->format('Y-m-d')
      ));
    } elseif (isset($this->min) && $date->getTimestamp() < $this->min->getTimestamp()) {
      throw new ValidateException('Date must be equal or greater than %s', $this->min->format('Y-m-d'));
    } elseif (isset($this->max) && $date->getTimestamp() > $this->max->getTimestamp()) {
      throw new ValidateException('Date must be equal or less than %s', $this->max->format('Y-m-d'));
    }

    return $date;
  }
}
