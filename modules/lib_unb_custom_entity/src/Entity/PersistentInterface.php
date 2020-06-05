<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityInterface;

/**
 * Interface for entities that should never be deleted.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
interface PersistentInterface extends EntityInterface {

  const FIELD_DELETED = 'deleted';

  /**
   * Terminate the entity, e.g. mark as "deleted".
   */
  public function terminate();

  /**
   * Whether the entity is considered "deleted".
   *
   * @param int|null $timestamp
   *   (optional) A UNIX timestamp. Set to determine the
   *   entity's existence at the given time. Otherwise
   *   the current existence will be determined.
   *
   * @return bool
   *   TRUE if the entity has been "deleted". FALSE otherwise.
   */
  public function isTerminated($timestamp = NULL);

  /**
   * Truly removes an entity permanently.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   In case of failures an exception is thrown.
   */
  public function destroy();

}
