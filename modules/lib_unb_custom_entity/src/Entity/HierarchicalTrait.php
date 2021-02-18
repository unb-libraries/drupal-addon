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
  public function setSuperior($entity) {
    if ($entity) {
      $this->set(HierarchicalInterface::FIELD_PARENT, $entity->id());
    }
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
    $fields[HierarchicalInterface::FIELD_PARENT] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t("Superior @entity", [
        '@entity' => $entity_type->getLabel(),
      ]))
      ->setRequired(FALSE)
      ->setDefaultValue(0)
      ->setRevisionable($entity_type->isRevisionable())
      ->setSetting('target_type', $entity_type->id());
    return $fields;
  }

}
