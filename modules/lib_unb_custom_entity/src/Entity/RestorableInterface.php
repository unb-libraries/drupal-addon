<?php

namespace Drupal\lib_unb_custom_entity\Entity;

/**
 * Interface for restorable entity implementations.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
interface RestorableInterface extends TerminableInterface {

  /**
   * Restore a previously terminated entity.
   *
   * @return $this
   *   The entity.
   */
  public function restore();

}
