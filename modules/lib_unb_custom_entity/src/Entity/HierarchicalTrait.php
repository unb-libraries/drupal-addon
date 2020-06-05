<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Trait to make entities members of a hierarchical structure.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
trait HierarchicalTrait {

  /**
   * {@inheritDoc}
   */
  public function getSuperior() {
    // TODO: Implement getSuperior() method.
  }

  /**
   * {@inheritDoc}
   */
  public function getSuperiors($max_asc = 1) {
    // TODO: Implement getSuperiors() method.
  }

  /**
   * {@inheritDoc}
   */
  public function getInferiors($max_desc = 1) {
    // TODO: Implement getInferiors() method.
  }

  /**
   * {@inheritDoc}
   */
  public function getFellows() {
    // TODO: Implement getFellows() method.
  }

  /**
   * Provides base field definitions to create a hierarchical entity structure.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   An array of base field definitions for the entity type, keyed by field
   *   name.
   */
  public static function hierarchyBaseFieldDefinitions(EntityTypeInterface $entity_type) {
    // TODO: Create "parent" baseFieldDefinition.
    return [];
  }

}
