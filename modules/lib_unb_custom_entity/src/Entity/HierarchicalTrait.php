<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

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
    return $this->get(HierarchicalInterface::FIELD_PARENT)
      ->entity;
  }

  /**
   * {@inheritDoc}
   */
  public function getSuperiors($max_asc = 1) {
    if ($max_asc > 0) {
      return [$this->getSuperior()] +
        $this->getSuperiors($max_asc - 1);
    }
    return [];
  }

  /**
   * {@inheritDoc}
   */
  public function getInferiors($max_desc = 1) {
    return $this
      ->getStorage()
      ->loadInferiors($this, $max_desc);
  }

  /**
   * {@inheritDoc}
   */
  public function getFellows() {
    return $this
      ->getStorage()
      ->loadFellows($this);
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
    $label = $entity_type->getLabel();
    $field_type = $entity_type->isRevisionable()
      ? 'entity_reference_revisions'
      : 'entity_reference';

    $fields[HierarchicalInterface::FIELD_PARENT] = BaseFieldDefinition::create($field_type)
      ->setLabel(t("Superior {$label}"))
      ->setRequired(FALSE)
      ->setDefaultValue(0)
      ->setSetting('target_type', $entity_type->id());

    return $fields;
  }

}
