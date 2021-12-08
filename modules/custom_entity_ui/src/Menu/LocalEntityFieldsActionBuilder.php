<?php

namespace Drupal\custom_entity_ui\Menu;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Menu\LocalActionDefault;

/**
 * Local action builder for content entities' fields settings.
 */
class LocalEntityFieldsActionBuilder extends LocalContentEntityActionBuilder {

  /**
   * {@inheritDoc}
   */
  public function buildLocalAction(EntityTypeInterface $entity_type) {
    $local_action = parent::buildLocalAction($entity_type);
    $local_action['class'] = LocalActionDefault::class;
    return $local_action;
  }

  /**
   * {@inheritDoc}
   */
  protected function getId(EntityTypeInterface $entity_type) {
    return "entity.{$entity_type->id()}.manage_fields";
  }

  /**
   * {@inheritDoc}
   */
  protected function getTitle(EntityTypeInterface $entity_type) {
    return $this->t('Manage fields');
  }

  /**
   * {@inheritDoc}
   */
  protected function getTargetRouteId(EntityTypeInterface $entity_type) {
    return "entity.{$entity_type->id()}.field_ui_fields";
  }

  /**
   * {@inheritDoc}
   */
  protected function getAppearsOn(EntityTypeInterface $entity_type) {
    $entity_type_id = $entity_type->id();
    return [
      "entity.{$entity_type_id}.canonical",
      "entity.{$entity_type_id}.add_form",
      "entity.{$entity_type_id}.edit_form",
    ];
  }


}
