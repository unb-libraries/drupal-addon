<?php

namespace Drupal\datetime_plus\DependencyInjection;

/**
 * Inject a storage datetime generator.
 *
 * @package Drupal\datetime_plus\DependencyInjection
 */
trait StorageTimeTrait {

  /**
   * Generate a datetime object according to the storage system's timezone.
   *
   * @return \Drupal\datetime_plus\Datetime\DateTimeFactoryInterface
   *   A datetime factory service instance.
   */
  protected static function storageTime() {
    return \Drupal::service('datetime_plus.storage');
  }

}
