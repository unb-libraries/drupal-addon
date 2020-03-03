<?php

namespace Drupal\datetime_plus\Datetime;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Extends Drupal's default datetime class.
 */
class DrupalDateTimePlus extends DrupalDateTime {

  const YEAR_TWO_DIGIT = 'y';
  const YEAR_FOUR_DIGIT = 'Y';
  const MONTH_NUMERIC = 'n';
  const MONTH_NUMERIC_ZERO = 'm';
  const MONTH_NATURAL = 'F';
  const MONTH_ABBR = 'M';
  const DAY_NATURAL = 'j';
  const DAY_ZERO = 'd';
  const WEEKDAY_NATURAL = 'l';
  const WEEKDAY_ABBR = 'D';
  const WEEKDAY_NUMERIC = 'j';

  /**
   * The year part as a two-digit string or four-digit integer.
   *
   * @param string $format
   *   The format, either two-digit or four-digit.
   *
   * @return int|string
   *   An integer (four-digit) or string (two-digit).
   */
  public function year($format = self::YEAR_FOUR_DIGIT) {
    switch ($format) {
      case self::YEAR_TWO_DIGIT:
        return $this->format(self::YEAR_TWO_DIGIT);

      case self::YEAR_FOUR_DIGIT:
      default:
        return intval($this->format(self::YEAR_FOUR_DIGIT));
    }
  }

  /**
   * The month part.
   *
   * @param string $format
   *   The format, either abbreviated or the full term.
   *
   * @return int|string
   *   An integer or string.
   */
  public function month($format = self::MONTH_NATURAL) {
    switch ($format) {
      case self::MONTH_NUMERIC:
      case self::MONTH_NUMERIC_ZERO:
        return intval($this->format($format));

      case self::MONTH_ABBR:
        return $this->format(self::MONTH_ABBR);

      case self::MONTH_NATURAL:
      default:
        return $this->format(self::MONTH_NATURAL);
    }
  }

  /**
   * The day part.
   *
   * @param string $format
   *   The format, numeric with or without leading zeros.
   *
   * @return int|string
   *   An integer (no leading zero) or string (leading zero).
   */
  public function day($format = self::DAY_NATURAL) {
    switch ($format) {
      case self::DAY_ZERO:
        return $this->format(self::DAY_ZERO);

      case self::DAY_NATURAL:
      default:
        return intval($this->format(self::DAY_NATURAL));
    }
  }

  /**
   * The day of the week.
   *
   * @param string $format
   *   The format, either numeric, abbreviated or the full term.
   *
   * @return int|string
   *   An integer or a string.
   */
  public function weekday($format = self::WEEKDAY_NATURAL) {
    switch ($format) {
      case self::WEEKDAY_NUMERIC:
        return intval($this->format(self::WEEKDAY_NUMERIC));

      case self::WEEKDAY_ABBR:
        return $this->format(self::WEEKDAY_ABBR);

      case self::WEEKDAY_NATURAL:
      default:
        return $this->format(self::WEEKDAY_NATURAL);
    }
  }

}
