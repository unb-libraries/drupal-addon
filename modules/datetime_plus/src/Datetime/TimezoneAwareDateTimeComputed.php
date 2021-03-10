<?php

namespace Drupal\datetime_plus\Datetime;

use Drupal\datetime\DateTimeComputed;

/**
 * A computed property for dates of timezone aware date time field items.
 *
 * @package Drupal\datetime_plus\Datetime
 */
class TimezoneAwareDateTimeComputed extends DateTimeComputed {

  /**
   * {@inheritDoc}
   */
  public function getValue() {
    parent::getValue();
    $this->setTimezone();
    return $this->date;
  }

  /**
   * Set to the timezone as configured in the data definition.
   */
  protected function setTimezone() {
    $timezone = $this->getDataDefinition()
      ->getSetting('timezone');
    $this->date->setTimezone($timezone);
  }

}
