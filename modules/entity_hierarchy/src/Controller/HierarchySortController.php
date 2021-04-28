<?php

namespace Drupal\entity_hierarchy\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\entity_hierarchy\Entity\SortableHierarchicalInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * (Re-)Calculates a hierarchical entity's sort order.
 *
 * @package Drupal\lib_unb_custom_entity\Controller
 */
class HierarchySortController extends ControllerBase {

  /**
   * The entity type.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface
   */
  protected $entityType;

  /**
   * The entity storage handler.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $entityStorage;

  /**
   * Get the entity type.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   An entity type definition.
   */
  protected function getEntityType() {
    return $this->entityType;
  }

  /**
   * Get the entity storage handler.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   A entity storage handler.
   */
  protected function entityStorage() {
    return $this->entityStorage;
  }

  /**
   * Create a HierarchySortController instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   An entity storage handler.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $entity_storage) {
    $this->entityType = $entity_type;
    $this->entityStorage = $entity_storage;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    $current_route = $container->get('current_route_match')
      ->getRouteObject();
    list($prefix, $entity_type_id) = explode(':', $current_route
      ->getOption('parameters')['entity_type_id']['type']);
    $entity_type_manager = $container->get('entity_type.manager');
    $entity_type = $entity_type_manager
      ->getDefinition($entity_type_id);
    $storage = $entity_type_manager
      ->getStorage($entity_type_id);
    return new static($entity_type, $storage);
  }

  /**
   * Sort entities of the type assigned to the controller.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response. The redirect destination is the "collection"
   *   route of the entity type assigned to the controller.
   */
  public function sort() {
    /** @var \Drupal\entity_hierarchy\Entity\SortableHierarchicalInterface[] $sortable_hierarchical_entities */
    $sortable_hierarchical_entities = $this->entityStorage()->loadMultiple();

    // Reset
    foreach ($sortable_hierarchical_entities as $sortable_hierarchical_entity) {
      $sortable_hierarchical_entity->set(SortableHierarchicalInterface::FIELD_SORT_KEY, NULL);
      $sortable_hierarchical_entity->save();
    }

    // Sort
    foreach ($sortable_hierarchical_entities as $sortable_hierarchical_entity) {
      $sortable_hierarchical_entity->get(SortableHierarchicalInterface::FIELD_SORT_KEY)
        ->applyDefaultValue();
      $sortable_hierarchical_entity->save();
    }

    return $this->redirect("entity.{$this->getEntityType()->id()}.collection");
  }

}
