<?php

namespace Drupal\custom_entity_ui\Menu;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Menu\LocalActionDefault;

/**
 * Local action builder for content entities' fields settings.
 */
class LocalEntityFieldsActionBuilder implements LocalEntityActionBuilderInterface {

  /**
   * {@inheritDoc}
   */
  public function buildLocalAction(EntityTypeInterface $entity_type) {
    $entity_type_id = $entity_type->id();
    return [
      'id' => "entity.{$entity_type_id}.manage_fields",
      'title' => t('Manage fields'),
      'route_name' => "entity.{$entity_type_id}.field_ui_fields",
      'class' => LocalActionDefault::class,
      'options' => [],
      'appears_on' => [
        "entity.{$entity_type_id}.canonical",
        "entity.{$entity_type_id}.add_form",
        "entity.{$entity_type_id}.edit_form",
      ],
      'weight' => 0,
    ];
  }


  /**
   * {@inheritDoc}
   */
  public function applies(EntityTypeInterface $entity_type) {
    return $entity_type->getGroup() === 'content';
  }
}
