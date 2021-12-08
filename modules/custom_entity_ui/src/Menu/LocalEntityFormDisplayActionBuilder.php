<?php

namespace Drupal\custom_entity_ui\Menu;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Local action builder for content entities' form display settings.
 */
class LocalEntityFormDisplayActionBuilder extends LocalContentEntityActionBuilder {

  /**
   * {@inheritDoc}
   */
  protected function getId(EntityTypeInterface $entity_type) {
    return "entity.{$entity_type->id()}.manage_form";
  }

  /**
   * {@inheritDoc}
   */
  protected function getTargetRouteId(EntityTypeInterface $entity_type) {
    return "entity.entity_form_display.{$entity_type->id()}.default";
  }

  /**
   * {@inheritDoc}
   */
  protected function getAppearsOn(EntityTypeInterface $entity_type) {
    $entity_type_id = $entity_type->id();
    return [
      "entity.{$entity_type_id}.add_form",
      "entity.{$entity_type_id}.edit_form",
    ];
  }
}
