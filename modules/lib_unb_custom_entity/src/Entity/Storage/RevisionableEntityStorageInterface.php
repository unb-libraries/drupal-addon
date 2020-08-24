<?php

namespace Drupal\lib_unb_custom_entity\Entity\Storage;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\RevisionableStorageInterface;

/**
 * Storage handler interface for revisionable entities.
 *
 * @package Drupal\lib_unb_custom_entity\Entity\Storage
 */
interface RevisionableEntityStorageInterface extends RevisionableStorageInterface {

  /**
   * Load all revisions of the given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return \Drupal\Core\Entity\RevisionableInterface
   *   An array of entity revisions.
   */
  public function loadEntityRevisions(EntityInterface $entity);

}
