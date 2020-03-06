<?php

namespace Drupal\datetime_plus\Datetime;

/**
 * Class for datetime intervals.
 *
 * As opposed to PHP's default \DateInterval, this
 * timespan features a fixed start and end time.
 *
 * @package Drupal\datetime_plus\Datetime
 */
class DateIntervalPlus {

  const UNIT_MAP = [
    'seconds' => 60,
    'minutes' => 60,
    'hours' => 24,
    'days' => 0,
    'months' => 12,
    'years' => 0,
  ];

  /**
   * The start of the timespan.
   *
   * @var \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   */
  protected $start;

  /**
   * The end of the timespan.
   *
   * @var \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   */
  protected $end;

  /**
   * The duration between start and end.
   *
   * @var \DateInterval
   */
  protected $duration;

  /**
   * Create a new date interval instance.
   *
   * @param \Drupal\datetime_plus\Datetime\DrupalDateTimePlus $start
   *   The start of the timespan.
   * @param \Drupal\datetime_plus\Datetime\DrupalDateTimePlus $end
   *   The end of the timespan.
   */
  public function __construct(DrupalDateTimePlus $start, DrupalDateTimePlus $end) {
    if ($start->getTimezone() !== $end->getTimezone()) {
      $end->setTimezone($start->getTimezone());
    }

    if ($end->getTimestamp() >= $start->getTimestamp()) {
      $this->start = $start;
      $this->end = $end;
    }
    else {
      $this->start = $end;
      $this->end = $start;
    }
  }

  /**
   * Retrieve the start of the timespan.
   *
   * @return \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   *   A datetime object.
   */
  public function start() {
    return $this->start;
  }

  /**
   * Retrieve the end of the timespan object.
   *
   * @return \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   *   A datetime object.
   */
  public function end() {
    return $this->end;
  }

  /**
   * Retrieve the duration of the timespan.
   *
   * @return \DateInterval
   *   A datetime interval object.
   */
  public function duration() {
    if (!isset($this->duration)) {
      $this->calculateDuration();
    }
    return $this->duration;
  }

  /**
   * Calculate the years, months, etc. between the start and end of the timespan.
   */
  private function calculateDuration() {
    $start = clone $this->start();
    $unit_map = self::UNIT_MAP;

    $unit_values = [];
    foreach (array_keys($unit_map) as $unit) {
      $unit_method = substr($unit, 0, strlen($unit) - 1);
      if (($s = $start->$unit_method()) <= ($e = $this->end()->$unit_method())) {
        $unit_values[$unit] = $e - $s;
      }
      else {
        $unit_values[$unit] = $e + $unit_map[$unit] - $s;
      }
      $start->add(\DateInterval::createFromDateString("{$unit_values[$unit]} {$unit}"));
      $unit_map['days'] = $start->daysInMonth();
    }

    $duration_description = implode(', ', array_map(function ($unit, $value) {
      return "{$value} {$unit}";
    }, array_keys($unit_values), array_values($unit_values)));

    $this->duration = \DateInterval::createFromDateString($duration_description);
  }

  /**
   * The number of entire years between start and end of the timespan.
   *
   * @return int
   *   An integer.
   */
  public function years() {
    return $this->duration()->y;
  }

  /**
   * The (relative) number of months between start and end of the timespan.
   *
   * @return int
   *   An integer between 0 and 11.
   */
  public function months() {
    return $this->duration()->m;
  }

  /**
   * The (relative) number of days between start and end of the timespan.
   *
   * @return int
   *   An integer between 0 and 30.
   */
  public function days() {
    return $this->duration()->d;
  }

  /**
   * The (relative) number of hours between start and end of the timespan.
   *
   * @return int
   *   An integer between 0 and 23.
   */
  public function hours() {
    return $this->duration()->h;
  }

  /**
   * The (relative) number of minutes between start and end of the timespan.
   *
   * @return int
   *   An integer between 0 and 59.
   */
  public function minutes() {
    return $this->duration()->i;
  }

  /**
   * The (relative) number of seconds between start and end of the timespan.
   *
   * @return int
   *   An integer between 0 and 59.
   */
  public function seconds() {
    return $this->duration()->s;
  }

  public function setTimeZone(\DateTimeZone $timezone) {
    $this->start()->setTimezone($timezone);
    $this->end()->setTimezone($timezone);
  }

}
