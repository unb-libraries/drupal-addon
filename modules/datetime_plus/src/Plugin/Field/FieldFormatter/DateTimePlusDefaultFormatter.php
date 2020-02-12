<?php

namespace Drupal\datetime_plus\Plugin\Field\FieldFormatter;

use Drupal\datetime\Plugin\Field\FieldFormatter\DateTimeDefaultFormatter;

/**
 * Plugin implementation of the 'Default' formatter for 'datetime_plus' fields.
 *
 * @FieldFormatter(
 *   id = "datetime_plus_default",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "datetime_plus"
 *   }
 * )
 */
class DateTimePlusDefaultFormatter extends DateTimeDefaultFormatter {
}