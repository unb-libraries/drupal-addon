<?php

namespace Drupal\custom_entity_ui\Menu;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Interface for local entity action builders.
 */
interface LocalEntityActionBuilderInterface {

  /**
   * Whether this builder builds local actions for the given entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   *
   * @return bool
   *   TRUE if the builder can produce local actions for the given entity
   *   type. FALSE otherwise.
   */
  public function applies(EntityTypeInterface $entity_type);

  /**
   * Build a local action for the given entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return array
   *   A local action definition array.
   */
  public function buildLocalAction(EntityTypeInterface $entity_type);

}
