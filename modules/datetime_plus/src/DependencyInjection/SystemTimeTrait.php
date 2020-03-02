<?php

namespace Drupal\datetime_plus\DependencyInjection;

/**
 * Inject a system datetime generator.
 *
 * @package Drupal\datetime_plus\DependencyInjection
 */
trait SystemTimeTrait {

  /**
   * Generate a datetime object according to the system's timezone.
   *
   * @return \Drupal\datetime_plus\Datetime\DateTimeFactoryInterface
   *   A datetime factory service instance.
   */
  protected function systemTime() {
    return \Drupal::service('datetime_plus.system');
  }

}
