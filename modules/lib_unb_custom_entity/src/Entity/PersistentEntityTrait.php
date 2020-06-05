<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Trait to make entities persistent.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
trait PersistentEntityTrait {

  /**
   * {@inheritDoc}
   */
  public function terminate() {
    // TODO: Implement terminate() method.
  }

  /**
   * {@inheritDoc}
   */
  public function isTerminated($timestamp = NULL) {
    // TODO: Implement isTerminated() method.
  }

  /**
   * {@inheritDoc}
   */
  public function destroy() {
    // TODO: Implement destroy() method.
  }

  /**
   * Provides base field definitions to create non-removable entities.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   An array of base field definitions for the entity type, keyed by field
   *   name.
   */
  public static function terminatedBaseFieldDefinitions(EntityTypeInterface $entity_type) {
    // TODO: Create "deleted" baseFieldDefinition.
    return [];
  }

}
