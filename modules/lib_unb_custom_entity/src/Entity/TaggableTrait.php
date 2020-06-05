<?php

namespace Drupal\lib_nb_custom_entity\Entity;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Trait to make content entities taggable.
 *
 * @package Drupal\lib_nb_custom_entity\Entity
 */
trait TaggableTrait {

  /**
   * {@inheritDoc}
   */
  public function getTags() {
    // TODO: Implement getTags() method.
  }

  /**
   * {@inheritDoc}
   */
  public function setTags($tags) {
    // TODO: Implement setTags() method.
  }

  /**
   * {@inheritDoc}
   */
  public function addTag($tag) {
    // TODO: Implement addTag() method.
  }

  /**
   * {@inheritDoc}
   */
  public function removeTag($tag) {
    // TODO: Implement removeTag() method.
  }

  /**
   * {@inheritDoc}
   */
  public function clearTags() {
    // TODO: Implement clearTags() method.
  }

  /**
   * Provides base field definitions to create taggable entities.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   An array of base field definitions for the entity type, keyed by field
   *   name.
   */
  public static function tagsBaseFieldDefinitions(EntityTypeInterface $entity_type) {
    // TODO: Create "parent" baseFieldDefinition.
    return [];
  }

}
