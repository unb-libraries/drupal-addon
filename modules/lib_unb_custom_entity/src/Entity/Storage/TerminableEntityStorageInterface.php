<?php

namespace Drupal\lib_unb_custom_entity\Entity\Storage;

use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Storage handler interface for persistent entity storage classes.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
interface TerminableEntityStorageInterface extends EntityStorageInterface {

  /**
   * Load terminated entities.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entities.
   */
  public function loadTerminated();

  /**
   * Load non-terminated entities.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   An array of entities.
   */
  public function loadCurrent();

}
