<?php

namespace Drupal\lib_unb_custom_entity\Entity\Storage;

use Drupal\Core\Entity\EntityInterface;

/**
 * Trait to enhance Drupal's default storage handler for revisionable entities.
 *
 * @package Drupal\lib_unb_custom_entity\Entity\Storage
 */
trait RevisionableEntityStorageTrait {

  /**
   * {@inheritDoc}
   */
  public function loadEntityRevisions(EntityInterface $entity) {
    /** @var \Drupal\lib_unb_custom_entity\Entity\Storage\RevisionableEntityStorageInterface $this */
    $database = \Drupal::database();

    $revision_id_key = $this->getEntityType()->getKey('revision');
    $revision_table = $this->getEntityType()->getRevisionTable();

    $revision_ids = $database->query(
      "SELECT {$revision_id_key} FROM {$revision_table} WHERE id = :entity_id", [
        ':entity_id' => $entity->id(),
      ]
    )->fetchCol();

    return $this->loadMultipleRevisions($revision_ids);
  }

}
