<?php

namespace Drupal\custom_entity_ui\Menu;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Local action builder for content entities' view display settings.
 */
class LocalEntityViewDisplayActionBuilder implements LocalEntityActionBuilderInterface {

  /**
   * {@inheritDoc}
   */
  public function buildLocalAction(EntityTypeInterface $entity_type) {
    $entity_type_id = $entity_type->id();
    return [
      'id' => "entity.{$entity_type_id}.manage_display",
      'title' => t('Manage display'),
      'route_name' => "entity.entity_view_display.{$entity_type_id}.default",
      'class' => DestinationAwareLocalAction::class,
      'options' => [],
      'appears_on' => [
        "entity.{$entity_type_id}.canonical",
      ],
      'cache_contexts' => [
        'url.path',
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
