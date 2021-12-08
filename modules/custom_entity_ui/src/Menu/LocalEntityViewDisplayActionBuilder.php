<?php

namespace Drupal\custom_entity_ui\Menu;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Local action builder for content entities' view display settings.
 */
class LocalEntityViewDisplayActionBuilder extends LocalContentEntityActionBuilder {

  /**
   * {@inheritDoc}
   */
  protected function getId(EntityTypeInterface $entity_type) {
  return "entity.{$entity_type->id()}.manage_display";
}

  /**
   * {@inheritDoc}
   */
  protected function getTargetRouteId(EntityTypeInterface $entity_type) {
  return "entity.entity_view_display.{$entity_type->id()}.default";
}

  /**
   * {@inheritDoc}
   */
  protected function getAppearsOn(EntityTypeInterface $entity_type) {
  $entity_type_id = $entity_type->id();
  return [
    "entity.{$entity_type_id}.canonical"
  ];
}


}
