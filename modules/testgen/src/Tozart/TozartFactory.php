<?php

namespace Drupal\testgen\Tozart;

use Tozart\Tozart;

/**
 * Factory to create the Tozart service.
 *
 * @package Drupal\testgen\Tozart
 */
class TozartFactory {

  /**
   * Create a Tozart instance.
   *
   * @param array $options
   *   Array of options to configure the created instance.
   *
   * @return \Tozart\Tozart
   *   A Tozart object.
   */
  public static function create(array $options = []) {
    return Tozart::instance();
  }

}
