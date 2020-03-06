<?php

namespace Drupal\datetime_plus\Datetime;

/**
 * Replaces PHP's \DateInterval for representing a relative time period.
 *
 * @package Drupal\datetime_plus\Datetime
 */
class Timespan {

  const UNIT_MAP = [
    'seconds' => 60,
    'minutes' => 60,
    'hours' => 24,
    'days' => 0,
    'months' => 12,
    'years' => 0,
  ];

  /**
   * The number entire of years.
   *
   * @var int
   */
  protected $years;

  /**
   * The number of entire months that do not make a whole year.
   *
   * @var int
   */
  protected $months;

  /**
   * The number of entire days that do not make a whole month.
   *
   * @var int
   */
  protected $days;

  /**
   * The number of entire hours that do not make a whole day.
   *
   * @var int
   */
  protected $hours;

  /**
   * The number of entire minutes that do not make a whole hour.
   *
   * @var int
   */
  protected $minutes;

  /**
   * The number of entire seconds that do not make a whole minute.
   *
   * @var int
   */
  protected $seconds;

  /**
   * Create a new timespan instance.
   *
   * @param $years
   *   Number of years.
   * @param $months
   *   Number of months.
   * @param $days
   *   Number of days.
   * @param $hours
   *   Number of hours.
   * @param $minutes
   *   Number of minutes.
   * @param $seconds
   *   Number of seconds.
   */
  private function __construct($years, $months, $days, $hours, $minutes, $seconds) {
    $this->years = $years;
    $this->months = $months;
    $this->days = $days;
    $this->hours = $hours;
    $this->minutes = $minutes;
    $this->seconds = $seconds;
  }

  /**
   * * Create a new timespan instance from a start and end time.
   *
   * @param \Drupal\datetime_plus\Datetime\DateIntervalPlus $interval
   *   The interval.
   *
   * @return \Drupal\datetime_plus\Datetime\Timespan
   *   A timespan object.
   */
  public static function createFromInterval(DateIntervalPlus $interval) {
    $start = clone $interval->start();
    $end = $interval->end();
    $unit_map = self::UNIT_MAP;

    $unit_values = [];
    foreach (array_keys($unit_map) as $unit) {
      $unit_method = substr($unit, 0, strlen($unit) - 1);
      if (($s = $start->$unit_method()) <= ($e = $end->$unit_method())) {
        $unit_values[$unit] = $e - $s;
      }
      else {
        $unit_values[$unit] = $e + $unit_map[$unit] - $s;
      }
      $start->add(\DateInterval::createFromDateString("{$unit_values[$unit]} {$unit}"));
      $unit_map['days'] = $start->daysInMonth();
    }

//    $duration_description = implode(', ', array_map(function ($unit, $value) {
//      return "{$value} {$unit}";
//    }, array_keys($unit_values), array_values($unit_values)));

    return Timespan::createFromArray($unit_values);
  }

  /**
   * Create a new timespan instance from an array.
   *
   * @param $parts
   *   An array containing values for each time unit.
   *
   * @return \Drupal\datetime_plus\Datetime\Timespan
   *   A timespan object.
   */
  public static function createFromArray($parts) {
    return new static(
      array_key_exists('years', $parts) ? $parts['years'] : 0,
      array_key_exists('months', $parts) ? $parts['months'] : 0,
      array_key_exists('days', $parts) ? $parts['days'] : 0,
      array_key_exists('hours', $parts) ? $parts['hours'] : 0,
      array_key_exists('minutes', $parts) ? $parts['minutes'] : 0,
      array_key_exists('seconds', $parts) ? $parts['seconds'] : 0
    );
  }

  /**
   * Create a new timespan instance from a PHP \DateInterval.
   *
   * @param \DateInterval $interval
   *   The interval.
   *
   * @return \Drupal\datetime_plus\Datetime\Timespan
   *   A timespan object.
   */
  public static function createFromDateInterval(\DateInterval $interval) {
    return new static(
      $interval->y,
      $interval->m,
      $interval->d,
      $interval->h,
      $interval->i,
      $interval->s
    );
  }

  /**
   * Create a new timespan instance from a string description.
   *
   * @param string $string
   *   A date string, e.g. '1 day', '2 years', or '1 month, 2 days'.
   *
   * @return \Drupal\datetime_plus\Datetime\Timespan
   *   A timespan object.
   */
  public static function createFromDateString($string) {
    return static::createFromDateInterval(\DateInterval::createFromDateString($string));
  }

  /**
   * The number entire of years.
   *
   * @return int
   *   An integer >= 0;
   */
  public function years() {
    return $this->years;
  }

  /**
   * The number of entire months that do not make a whole year.
   *
   * @return int
   *   An integer between 0 and 11.
   */
  public function months() {
    return $this->months;
  }

  /**
   * The number of entire days that do not make a whole month.
   *
   * @return int
   *   An integer between 0 and 30.
   */
  public function days() {
    return $this->days;
  }

  /**
   * The number of entire hours that do not make a whole day.
   *
   * @return int
   *   An integer between 0 and 23.
   */
  public function hours() {
    return $this->hours;
  }

  /**
   * The number of entire minutes that do not make a whole hour.
   *
   * @return int
   *   An integer between 0 and 59.
   */
  public function minutes() {
    return $this->minutes;
  }

  /**
   * The number of entire seconds that do not make a whole minute.
   *
   * @return int
   *   An integer between 0 and 59.
   */
  public function seconds() {
    return $this->seconds;
  }

  /**
   * Whether the timespan is shorter than the one given.
   *
   * @param \Drupal\datetime_plus\Datetime\Timespan $other
   *   The timespan to compare to.
   *
   * @return bool
   *   TRUE if this timespan is shorter. FALSE otherwise.
   */
  public function isShorterThan(Timespan $other) {
    return self::compare($this, $other) < 0;
  }

  /**
   * Whether the timespan is longer than the one given.
   *
   * @param \Drupal\datetime_plus\Datetime\Timespan $other
   *   The timespan to compare to.
   *
   * @return bool
   *   TRUE if this timespan is longer. FALSE otherwise.
   */
  public function isLongerThan(Timespan $other) {
    return self::compare($this, $other) > 0;
  }

  /**
   * Whether the timespan is as long as the one given.
   *
   * @param \Drupal\datetime_plus\Datetime\Timespan $other
   *   The timespan to compare to.
   *
   * @return bool
   *   TRUE if this timespan is as long as the one given.
   *   FALSE otherwise.
   */
  public function equals(Timespan $other) {
    return self::compare($this, $other) === 0;
  }

  /**
   * Compares the length of two timespan objects.
   *
   * @param \Drupal\datetime_plus\Datetime\Timespan $a
   *   Contestant A.
   * @param \Drupal\datetime_plus\Datetime\Timespan $b
   *   Contestant B.
   *
   * @return int
   *   A negative integer, if A is shorter than B.
   *   A positive integer, if A is longer than B.
   *   0, if both A and B are equally long.
   */
  private static function compare(Timespan $a, Timespan $b) {
    $av = $a->toArray();
    $bv = $b->toArray();

    foreach ($bv as $key => $value) {
      if (($diff = $av[$key] - $bv[$key]) !== 0) {
        return $diff;
      }
    }
    return 0;
  }

  /**
   * Convert the timespan into an array.
   *
   * @return array
   *   An array of the form TIME_UNIT => VALUE.
   */
  public function toArray() {
    return [
      'years' => $this->years,
      'months' => $this->months,
      'days' => $this->days,
      'hours' => $this->hours,
      'minutes' => $this->minutes,
      'seconds' => $this->seconds,
    ];
  }


}