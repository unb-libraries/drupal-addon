<?php

namespace Drupal\datetime_plus\Plugin\Field\FieldType;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Plugin implementation of the 'datetime_plus' field type.
 *
 * @FieldType(
 *   id = "datetime_plus",
 *   label = @Translation("Datetime Plus"),
 *   description = @Translation("Create and store date values."),
 *   default_widget = "datetime_plus_default",
 *   default_formatter = "datetime_plus_default",
 *   list_class = "\Drupal\datetime\Plugin\Field\FieldType\DateTimeFieldItemList",
 *   constraints = {"DateTimeFormat" = {}}
 * )
 */
class DateTimePlusItem extends DateTimeItem {

  /**
   * {@inheritDoc}
   */
  public function setValue($values, $notify = TRUE) {
    if (is_array($values)) {
      foreach ($values as $index => $value) {
        if ($values instanceof DateTimePlus) {
          $values[$index] = $this->toStorageValue($value);
        }
      }
    }
    elseif ($values instanceof DateTimePlus) {
      $values = $this->toStorageValue($values);
    }
    parent::setValue($values, $notify);
  }

  /**
   * Convert the datetime object to a value that can be stored in a database.
   *
   * @param \Drupal\Component\Datetime\DateTimePlus $datetime
   *   The datetime object.
   *
   * @return string
   *   Datetime formatted string.
   */
  protected function toStorageValue(DateTimePlus $datetime) {
    $timezone = new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);
    return $datetime
      ->setTimezone($timezone)
      ->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
  }

}
