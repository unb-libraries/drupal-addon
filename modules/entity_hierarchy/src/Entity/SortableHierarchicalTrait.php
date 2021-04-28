<?php

namespace Drupal\entity_hierarchy\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use function t;

/**
 * Trait to make hierarchical entities sortable.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
trait SortableHierarchicalTrait {

  use HierarchicalTrait;

  /**
   * {@inheritDoc}
   */
  public function getSortKey() {
    return $this->get(SortableHierarchicalInterface::FIELD_SORT_KEY)
      ->value;
  }

  /**
   * Set the sort key to the given value.
   *
   * @param string $sort_key
   *   A string.
   */
  protected function setSortKey(string $sort_key) {
    $this->set(SortableHierarchicalInterface::FIELD_SORT_KEY, $sort_key);
  }

  /**
   * Provides the base field definition to create a sort key field.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   A "sort_key" base field definition for the given entity type.
   */
  public static function sortKeyBaseFieldDefinition(EntityTypeInterface $entity_type) {
    return BaseFieldDefinition::create('hierarchy_sort')
      ->setLabel(t('Hierarchy sort key'))
      ->setRequired(TRUE)
      ->setRevisionable(FALSE);
  }

}
