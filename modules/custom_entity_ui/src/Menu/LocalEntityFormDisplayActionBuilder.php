<?php

namespace Drupal\custom_entity_ui\Menu;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Local action builder for content entities' form display settings.
 */
class LocalEntityFormDisplayActionBuilder implements LocalEntityActionBuilderInterface {

  /**
   * {@inheritDoc}
   */
  public function buildLocalAction(EntityTypeInterface $entity_type) {
    $entity_type_id = $entity_type->id();
    return [
      'id' => "entity.{$entity_type_id}.manage_form",
      'title' => t('Manage form display'),
      'route_name' => "entity.entity_form_display.{$entity_type_id}.default",
      'class' => DestinationAwareLocalAction::class,
      'options' => [],
      'appears_on' => [
        "entity.{$entity_type_id}.add_form",
        "entity.{$entity_type_id}.edit_form",
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
