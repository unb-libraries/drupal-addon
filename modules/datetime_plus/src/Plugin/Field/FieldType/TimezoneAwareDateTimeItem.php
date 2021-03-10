<?php

namespace Drupal\datetime_plus\Plugin\Field\FieldType;

use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;

/**
 * Plugin implementation of the 'datetime_plus' field type.
 *
 * @FieldType(
 *   id = "datetime_timezone",
 *   label = @Translation("Date (custom timezone)"),
 *   description = @Translation("Create and store timezone-customizable date values."),
 *   default_widget = "datetime_default",
 *   default_formatter = "datetime_default",
 *   list_class = "\Drupal\datetime\Plugin\Field\FieldType\DateTimeFieldItemList",
 *   constraints = {"DateTimeFormat" = {}}
 * )
 */
class TimezoneAwareDateTimeItem extends DateTimeItem {

}
