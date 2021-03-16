<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Enhance a fieldable entity to reference the user who last edited it.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
trait UserEditedTrait {

  /**
   * {@inheritDoc}
   */
  public function getEditor() {
    return $this->get(UserEditableInterface::FIELD_EDITOR)
      ->entity;
  }

  /**
   * Base field definition for a "editor" field.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to which the field will be attached.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   A base field definition.
   */
  protected static function editorBaseFieldDefinition(EntityTypeInterface $entity_type) {
    return BaseFieldDefinition::create('editor')
      ->setLabel(t('Editor'))
      ->setDescription(t('The user who last edited the entity.'))
      ->setRevisionable(TRUE);
  }

}
