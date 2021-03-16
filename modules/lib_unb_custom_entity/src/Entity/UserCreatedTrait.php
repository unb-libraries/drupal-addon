<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Enhance a fieldable entity to reference the user who created it.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
trait UserCreatedTrait {

  /**
   * {@inheritDoc}
   */
  public function getCreator() {
    return $this->get(UserCreatableInterface::FIELD_CREATOR)
      ->entity;
  }

  /**
   * Base field definition for a "creator" field.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to which the field will be attached.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   A base field definition.
   */
  protected static function creatorBaseFieldDefinition(EntityTypeInterface $entity_type) {
    return BaseFieldDefinition::create('creator')
      ->setLabel(t('Creator'))
      ->setDescription(t('The user who created the entity.'));
  }

}
