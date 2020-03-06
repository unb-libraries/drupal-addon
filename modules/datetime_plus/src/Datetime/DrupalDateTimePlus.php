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
  const HOUR_24 = 'H';
  const HOUR_24_ZERO = 'G';
  const HOUR_12 = 'h';
  const HOUR_12_ZERO = 'g';
  const MINUTE_NATURAL = '_i';
  const MINUTE_ZERO = 'i';
  const SECOND_NATURAL = '_s';
  const SECOND_ZERO = 's';

  /**
   * The date part.
   *
   * @param string $format
   *   The format. Defaults to YYYY-MM-DD.
   *
   * @return string
   *   A date formatted string.
   */
  public function date($format = 'Y-m-d') {
    return $this->format($format);
  }

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
  public function month($format = self::MONTH_NUMERIC) {
    switch ($format) {
      case self::MONTH_NATURAL:
      case self::MONTH_ABBR:
      case self::MONTH_NUMERIC_ZERO:
        return $this->format($format);

      case self::MONTH_NUMERIC:
      default:
        return intval($this->format($format));
    }
  }

  /**
   * The number of days of the month the datetime object lies within.
   *
   * @return int
   *   An integer between 28 and 31.
   */
  public function daysInMonth() {
    $month = $this->month();
    return $month === 2
      ? 28 + intval($this->isLeapYear())
      : $month > 7
        ? 31 - $month % 2
        : 30 + $month % 2;
  }

  /**
   * The number of days in the year the datetime object lies within.
   *
   * @return int
   *   Either 365 or 366.
   */
  public function daysInYear() {
    return 365 + intval($this->isLeapYear());
  }

  /**
   * Whether the datetime object lies within a leap year.
   *
   * @return bool
   *   TRUE if the year is a leap year. FALSE otherwise.
   */
  public function isLeapYear() {
    $year = $this->year();
    return $year % 4 === 0 && ($year % 100 !== 0 || $year % 400 === 0);
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

  /**
   * The time part.
   *
   * @param string $format
   *   The format. Defaults to 12-hour (H:MM AM/PM) format.
   *
   * @return string
   *   A time formatted string.
   */
  public function time($format = 'h:i A') {
    return $this->format($format);
  }

  /**
   * The hour.
   *
   * @param string $format
   *   The format, whether to return 12 or 24 hour format,
   *   include or exclude leading zeros.
   *
   * @return int|string
   *   An integer, if excluding leading zeros.
   *   A string otherwise.
   */
  public function hour($format = self::HOUR_24) {
    switch ($format) {
      case self::HOUR_12_ZERO:
      case self::HOUR_24_ZERO:
        return $this->format($format);

      case self::HOUR_12:
      case self::HOUR_24:
      default:
        return intval($this->format($format));
    }
  }

  /**
   * The minute.
   *
   * @param string $format
   *   The format, whether to include or exclude
   *   leading zeros.
   *
   * @return int|string
   *   An integer if excluding leading zeros.
   *   A string otherwise.
   */
  public function minute($format = self::MINUTE_NATURAL) {
    $minute = $this->format('i');
    switch ($format) {
      case self::MINUTE_ZERO:
        return $minute;

      case self::MINUTE_NATURAL:
      default:
        return intval($minute);
    }
  }

  /**
   * The second.
   *
   * @param string $format
   *   The format, whether to include or exclude
   *   leading zeros.
   *
   * @return int|string
   *   An integer if excluding leading zeros.
   *   A string otherwise.
   */
  public function second($format = self::SECOND_NATURAL) {
    $second = $this->format('s');
    switch ($format) {
      case self::SECOND_ZERO:
        return $second;

      case self::SECOND_NATURAL:
      default:
        return intval($second);
    }
  }

}
