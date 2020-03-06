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

  /**
   * The start of the interval.
   *
   * @var \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   */
  protected $start;

  /**
   * The end of the interval.
   *
   * @var \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   */
  protected $end;

  /**
   * The duration between start and end of the interval.
   *
   * @var \Drupal\datetime_plus\Datetime\Timespan
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
   * Retrieve the duration of the interval.
   *
   * @return \Drupal\datetime_plus\Datetime\Timespan
   *   A timespan object.
   */
  public function duration() {
    if (!isset($this->duration)) {
      $this->duration = Timespan::createFromInterval($this);
    }
    return $this->duration;
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
