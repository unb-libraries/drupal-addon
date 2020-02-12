<?php

namespace Drupal\datetime_plus\Plugin\Field\FieldWidget;

use Drupal\datetime\Plugin\Field\FieldWidget\DateTimeDefaultWidget;

/**
 * Plugin implementation of the 'datetime_plus_default' widget.
 *
 * @FieldWidget(
 *   id = "datetime_plus_default",
 *   label = @Translation("Date and time"),
 *   field_types = {
 *     "datetime_plus",
 *   }
 * )
 */
class DateTimePlusDefaultWidget extends DateTimeDefaultWidget {
}