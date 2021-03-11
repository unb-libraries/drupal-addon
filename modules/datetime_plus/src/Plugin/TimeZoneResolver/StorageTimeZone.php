<?php

namespace Drupal\datetime_plus\Plugin\TimeZoneResolver;

use Drupal\Core\Plugin\PluginBase;
use Drupal\datetime_plus\Plugin\Field\FieldType\TimezoneAwareDateTimeItemInterface;

/**
 * Resolves to the timezone used for storage.
 *
 * @DateTimeZoneResolver(
 *   id = "storage",
 *   label = @Translation("Storage timezone"),
 * )
 *
 * @package Drupal\datetime_plus\Plugin\TimeZoneResolver
 */
class StorageTimeZone extends DateTimeZoneResolverBase {

  /**
   * {@inheritDoc}
   */
  public function getTimeZone() {
    $timezone_name = TimezoneAwareDateTimeItemInterface::STORAGE_TIMEZONE;
    return new \DateTimeZone($timezone_name);
  }

}
