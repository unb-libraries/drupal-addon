<?php

namespace Drupal\custom_entity_ui\Menu;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Menu\LocalActionDefault;

/**
 * Base class for local content entity action builders.
 *
 * @package Drupal\custom_entity_ui\Menu
 */
abstract class LocalContentEntityActionBuilder implements LocalEntityActionBuilderInterface {

  /**
   * {@inheritDoc}
   */
  public function buildLocalAction(EntityTypeInterface $entity_type) {
    return [
      'id' => $this->getId($entity_type),
      'title' => t('Manage fields'),
      'route_name' => $this->getTargetRouteId($entity_type),
      'class' => DestinationAwareLocalAction::class,
      'options' => [],
      'appears_on' => $this->getAppearsOn($entity_type),
      'weight' => 0,
    ];
  }

  /**
   * Get the ID of the local action.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   *
   * @return string
   *   A string.
   */
  abstract protected function getId(EntityTypeInterface $entity_type);

  /**
   * Get the ID of the route the action should point at.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   *
   * @return string
   *   A route ID.
   */
  abstract protected function getTargetRouteId(EntityTypeInterface $entity_type);

  /**
   * Get the route IDs the action should appear on.
   *
   * @return array
   *   An array of route IDs.
   */
  abstract protected function getAppearsOn(EntityTypeInterface $entity_type);

  /**
   * {@inheritDoc}
   */
  public function applies(EntityTypeInterface $entity_type) {
    return $entity_type->getGroup() === 'content';
  }

}
