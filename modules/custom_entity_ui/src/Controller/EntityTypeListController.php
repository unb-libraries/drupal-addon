<?php

namespace Drupal\custom_entity_ui\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Controller rendering a list of custom entity types.
 *
 * @package Drupal\custom_entity_ui\Controller
 */
class EntityTypeListController extends ControllerBase {

  /**
   * Creates a list of entity types.
   *
   * @return array
   *   A render element.
   */
  public function listing() {
    return [
      '#type' => 'table',
      '#header' => $this->buildHeader(),
      '#rows' => $this->buildRows(),
      '#empty' => $this->emptyMessage(),
    ];
  }

  /**
   * Builds the header row for the entity type listing.
   *
   * @return array
   *   A render array structure of header strings.
   *
   * @see \Drupal\custom_entity_ui\Controller\EntityTypeListController::listing()
   */
  protected function buildHeader() {
    return [
      'machine_name' => $this->t('Machine name'),
      'name' => $this->t('Name'),
      'provider' => $this->t('Defined by'),
    ];
  }

  /**
   * Builds a row for each entity type in the entity type listing.
   *
   * @return array
   *   A render array structure of properties for each entity type.
   *
   * @see \Drupal\custom_entity_ui\Controller\EntityTypeListController::listing()
   */
  protected function buildRows() {
    $rows = [];
    foreach ($this->loadEntityTypes() as $entity_type) {
      $rows[] = $this->buildRow($entity_type);
    }
    return $rows;
  }

  /**
   * Builds a row for an entity type in the entity type listing.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type for this row of the list.
   *
   * @return array
   *   A render array structure of properties for the given entity type.
   */
  protected function buildRow(EntityTypeInterface $entity_type) {
    return [
      'id' => $entity_type->id(),
      'name' => $entity_type->getLabel(),
      'provider' => $entity_type->getProvider(),
    ];
  }

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

  /**
   * The message to display when the list contains no entries.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   *   A (translatable) string.
   */
  protected function emptyMessage() {
    return $this->t('No custom entity types have been defined.');
  }

}
