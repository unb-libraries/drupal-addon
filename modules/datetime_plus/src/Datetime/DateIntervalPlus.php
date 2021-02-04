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
class DateIntervalPlus implements IntervalInterface {

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
   * {@inheritDoc}
   */
  public function start() {
    return $this->start;
  }

  /**
   * {@inheritDoc}
   */
  public function setStart(DrupalDateTimePlus $start) {
    if ($this->start()->getTimezone()->getName() !== $start->getTimezone()->getName()) {
      $start->setTimezone($this->start()->getTimezone());
    }

    if ($start > $this->end()) {
      $this->setEnd($start);
    }
    $this->start = $start;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function setEnd(DrupalDateTimePlus $end) {
    if ($this->end()->getTimezone()->getName() !== $end->getTimezone()->getName()) {
      $end->setTimezone($this->end()->getTimezone());
    }

    if ($end < $this->start()) {
      $this->setStart($end);
    }
    $this->end = $end;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function end() {
    return $this->end;
  }

  /**
   * {@inheritDoc}
   */
  public function duration() {
    if (!isset($this->duration)) {
      $this->duration = Timespan::createFromInterval($this);
    }
    return $this->duration;
  }

  /**
   * {@inheritDoc}
   */
  public function years() {
    return $this->duration()->y;
  }

  /**
   * {@inheritDoc}
   */
  public function months() {
    return $this->duration()->m;
  }

  /**
   * {@inheritDoc}
   */
  public function days() {
    return $this->duration()->d;
  }

  /**
   * {@inheritDoc}
   */
  public function hours() {
    return $this->duration()->h;
  }

  /**
   * {@inheritDoc}
   */
  public function minutes() {
    return $this->duration()->i;
  }

  /**
   * {@inheritDoc}
   */
  public function seconds() {
    return $this->duration()->s;
  }

  /**
   * {@inheritDoc}
   */
  public function setTimeZone(\DateTimeZone $timezone) {
    $this->start()->setTimezone($timezone);
    $this->end()->setTimezone($timezone);
  }

}
