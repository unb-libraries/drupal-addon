<?php

namespace Drupal\custom_entity_ui\Controller;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Url;

/**
 * Renders a table of entity types.
 *
 * @package Drupal\custom_entity_ui\Controller
 */
class EntityTypeTableController extends EntityTypeListController {

  /**
   * Creates a table of entity types.
   *
   * @return array
   *   A render array structure.
   */
  public function listing() {
    $entity_types = $this->loadEntityTypes();
    return [
      '#type' => 'table',
      '#header' => $this->buildHeader(),
      '#rows' => $this->buildRows($entity_types),
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
      'operations' => $this->t('Operations')
    ];
  }

  /**
   * Builds a row for each entity type in the entity type listing.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface[] $entity_types
   *   An array of entity types.
   *
   * @return array
   *   A render array structure of properties for each entity type.
   *
   * @see \Drupal\custom_entity_ui\Controller\EntityTypeListController::listing()
   */
  protected function buildRows(array $entity_types) {
    $rows = [];
    foreach ($entity_types as $entity_type) {
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
      'operations' => [
        'data' => $this->buildOperations($entity_type)
      ],
    ];
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

  /**
   * Builds a renderable list of operations for the entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type on which the operations will be performed.
   *
   * @return array
   *   A renderable array of operation links.
   *
   * @see \Drupal\custom_entity_ui\Controller\EntityTypeTableController::buildRows()
   */
  protected function buildOperations(EntityTypeInterface $entity_type) {
    return [
      '#type' => 'operations',
      '#links' => $this->buildLinks($entity_type),
    ];
  }

  /**
   * Builds a renderable list of operation links for the entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to which the links will point.
   *
   * @return array
   *   A renderable array of links.
   */
  protected function buildLinks(EntityTypeInterface $entity_type) {
    $links = [];

    if ($field_ui_base_route_name = $entity_type->get('field_ui_base_route')) {
      if ($this->isBundled($entity_type) && !empty($bundles = $this->getBundles($entity_type))) {
        foreach ($bundles as $bundle_id => $bundle) {
          $links["edit-{$bundle->id()}"] = [
            'title' => $this->t('Edit @bundle', [
              '@bundle' => $bundle->label(),
            ]),
            'weight' => array_keys($bundles)[$bundle_id],
            'url' => Url::fromRoute($field_ui_base_route_name, [
              $bundle->getEntityType()->id() => $bundle->id(),
            ]),
          ];
        }
      }
      elseif (!$this->isBundled($entity_type)) {
        $links['edit'] = [
          'title' => $this->t('Edit'),
          'weight' => 10,
          'url' => Url::fromRoute($field_ui_base_route_name),
        ];
      }
    }
    return $links;
  }

  /**
   * Whether the entity type has bundles.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return bool
   *   TRUE if the entity type can be bundled, even if no instances of the
   *   bundle type exist. FALSE if the entity type cannot be bundled.
   */
  protected function isBundled(EntityTypeInterface $entity_type) {
    return !is_null($entity_type->getBundleEntityType());
  }

  /**
   * Get bundle instances for the entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Drupal\Core\Config\Entity\ConfigEntityInterface[]
   *   An array of bundle entities.
   */
  protected function getBundles(EntityTypeInterface $entity_type) {
    if ($storage = $this->getBundleStorage($entity_type)) {
      /** @var \Drupal\Core\Config\Entity\ConfigEntityInterface[] $bundles */
      $bundles = $storage->loadMultiple();
      return $bundles;
    }
    else {
      $this->messenger()
        ->addError($this->t("@entity_type uses a bundle type that does not exist.", [
          '@entity_type' => $entity_type->getLabel(),
        ]));
      return [];
    }
  }

  /**
   * Get the storage handler for the entity type's bundle type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface|null
   *   An entity storage handler. NULL if none could be found, e.g. the
   *   defined bundle type does not exist or references a storage handler
   *   that does not exist.
   */
  protected function getBundleStorage(EntityTypeInterface $entity_type) {
    try {
      $bundle_type_id = $entity_type->getBundleEntityType();
      return $this->entityTypeManager()
        ->getStorage($bundle_type_id);
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

}
