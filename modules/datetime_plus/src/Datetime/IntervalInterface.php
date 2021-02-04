<?php

namespace Drupal\datetime_plus\Datetime;

/**
 * Interface for date intervals.
 *
 * @package Drupal\datetime_plus\Datetime
 */
interface IntervalInterface {

  /**
   * Retrieve the start of the timespan.
   *
   * @return \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   *   A datetime object.
   */
  public function start();

  /**
   * Set the start of the timespan.
   *
   * @param \Drupal\datetime_plus\Datetime\DrupalDateTimePlus $start
   *   A date time object.
   *
   * @return static
   */
  public function setStart(DrupalDateTimePlus $start);

  /**
   * Retrieve the end of the timespan object.
   *
   * @return \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   *   A datetime object.
   */
  public function end();

  /**
   * Set the end of the timespan.
   *
   * @param \Drupal\datetime_plus\Datetime\DrupalDateTimePlus $end
   *   A date time object.
   *
   * @return static
   */
  public function setEnd(DrupalDateTimePlus $end);

  /**
   * Retrieve the duration of the interval.
   *
   * @return \Drupal\datetime_plus\Datetime\Timespan
   *   A timespan object.
   */
  public function duration();

  /**
   * The number of entire years between start and end of the timespan.
   *
   * @return int
   *   An integer.
   */
  public function years();

  /**
   * The (relative) number of months between start and end of the timespan.
   *
   * @return int
   *   An integer between 0 and 11.
   */
  public function months();

  /**
   * The (relative) number of days between start and end of the timespan.
   *
   * @return int
   *   An integer between 0 and 30.
   */
  public function days();

  /**
   * The (relative) number of hours between start and end of the timespan.
   *
   * @return int
   *   An integer between 0 and 23.
   */
  public function hours();

  /**
   * The (relative) number of minutes between start and end of the timespan.
   *
   * @return int
   *   An integer between 0 and 59.
   */
  public function minutes();

  /**
   * The (relative) number of seconds between start and end of the timespan.
   *
   * @return int
   *   An integer between 0 and 59.
   */
  public function seconds();

  /**
   * Set the timezone of the date interval, i.e. of the start and end dates.
   *
   * @param \DateTimeZone $timezone
   *   A date timezone object.
   */
  public function setTimeZone(\DateTimeZone $timezone);

}
