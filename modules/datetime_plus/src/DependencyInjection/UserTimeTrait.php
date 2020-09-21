<?php

namespace Drupal\datetime_plus\DependencyInjection;

/**
 * Inject a user datetime generator.
 *
 * @package Drupal\datetime_plus\DependencyInjection
 */
trait UserTimeTrait {

  /**
   * Generate a datetime object according to the user's timezone.
   *
   * @return \Drupal\datetime_plus\Datetime\DateTimeFactoryInterface
   *   A datetime factory service instance.
   */
  protected static function userTime() {
    return \Drupal::service('datetime_plus.user');
  }

}
