<?php

namespace Drupal\drupal_trupal\Trupal;

/**
 * Provides dependency injection for the Trupal service.
 */
trait TrupalTrait {

  /**
   * Inject the Trupal service.
   *
   * @return \Trupal\Trupal
   *   A Trupal instance.
   */
  protected static function trupal() {
    return \Drupal::service('trupal');
  }

}
