<?php

namespace Drupal\lib_unb_custom_entity\Entity\Storage;

use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Storage handler interface for persistent entity storage classes.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
interface PersistentEntityStorageInterface extends EntityStorageInterface {

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

  /**
   * Terminates the given entities.
   *
   * @param array $entities
   *   An array of entity objects to terminate.
   */
  public function terminate(array $entities);

  /**
   * Truly deletes permanently saved entities.
   *
   * @param array $entities
   *   An array of entity objects to destroy.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   In case of failures, an exception is thrown.
   */
  public function destroy(array $entities);

}
