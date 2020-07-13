<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Trait to make entities persistent.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
trait TerminableTrait {

  /**
   * {@inheritDoc}
   */
  public function terminate() {
    return $this->set(TerminableInterface::FIELD_DELETED, time())
      ->save();
  }

  /**
   * {@inheritDoc}
   */
  public function isTerminated($timestamp = NULL) {
    if (!$timestamp) {
      $timestamp = time();
    }
    if ($deleted = $this->get(TerminableInterface::FIELD_DELETED)->value) {
      return $timestamp >= $deleted;
    }
    return FALSE;
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
    $fields[TerminableInterface::FIELD_DELETED] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Deleted'))
      ->setDescription(t('When the entity was marked as deleted.'))
      ->setRequired(FALSE)
      ->setRevisionable($entity_type->isRevisionable());

    return $fields;
  }

}
