<?php

namespace Drupal\drupal_trupal\Trupal;

use Trupal\Trupal;

/**
 * Factory to create the Trupal service.
 *
 * @package Drupal\drupal_trupal\Trupal
 */
class TrupalFactory {

  /**
   * Create a Trupal instance.
   *
   * @param array $options
   *   Array of options to configure the created instance.
   *
   * @return \Trupal\Trupal
   *   A Trupal object.
   */
  public static function create(array $options = []) {
    return Trupal::instance();
  }

}
