<?php

namespace Drupal\custom_entity_ui\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Controller rendering a list of custom entity types.
 *
 * @package Drupal\custom_entity_ui\Controller
 */
abstract class EntityTypeListController extends ControllerBase {

  /**
   * Creates a list of entity types.
   *
   * @return array
   *   A render element.
   */
  abstract public function listing();

  /**
   * Load the entity types to contain in this listing.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface[]
   *   An array of entity types keyed by their machine name.
   */
  protected function loadEntityTypes() {
    $entity_types = $this->entityTypeManager()->getDefinitions();
    return array_filter($entity_types, [$this, 'filter']);
  }

  /**
   * Whether the given entity type should be contained in the listing.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   *
   * @return bool
   *   Always TRUE, i.e. all entity types are contained.
   */
  protected function filter(EntityTypeInterface $entity_type) {
    return $entity_type->getGroup() === 'content';
  }

}
