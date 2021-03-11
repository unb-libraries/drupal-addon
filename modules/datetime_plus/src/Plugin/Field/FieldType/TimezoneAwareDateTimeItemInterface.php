<?php

namespace Drupal\datetime_plus\Plugin\Field\FieldType;

use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Interface for timezone aware datetime field items.
 *
 * @package Drupal\datetime_plus\Plugin\Field\FieldType
 */
interface TimezoneAwareDateTimeItemInterface extends DateTimeItemInterface {

  /**
   * Get the timezone of the date item.
   *
   * @return \DateTimeZone
   *   A timezone object.
   */
  public function getTimeZone();

  /**
   * Get the computed date time object.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   A Drupal datetime object.
   */
  public function getDateTime();

}
