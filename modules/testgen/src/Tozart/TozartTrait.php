<?php

namespace Drupal\testgen\Tozart;

/**
 * Provides dependency injection for the Tozart service.
 */
trait TozartTrait {

  /**
   * Inject the Tozart service.
   *
   * @return \Tozart\Tozart
   *   A tozart instance.
   */
  protected static function tozart() {
    return \Drupal::service('tozart');
  }

}
