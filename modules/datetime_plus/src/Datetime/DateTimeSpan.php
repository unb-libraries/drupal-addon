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
class DateTimeSpan {

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
   * Create a new DateTimeSpan instance.
   *
   * @param \Drupal\datetime_plus\Datetime\DrupalDateTimePlus $start
   *   The start of the timespan.
   * @param \Drupal\datetime_plus\Datetime\DrupalDateTimePlus $end
   *   The end of the timespan.
   */
  public function __construct(DrupalDateTimePlus $start, DrupalDateTimePlus $end) {
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
    return $this->end()->diff($this->start());
  }

  public function setTimeZone(\DateTimeZone $timezone) {
    $this->start()->setTimezone($timezone);
    $this->end()->setTimezone($timezone);
  }

}
