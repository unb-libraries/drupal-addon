<?php

namespace Drupal\custom_entity_ui\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Controller rendering a list of custom entity types.
 *
 * @package Drupal\custom_entity_ui\Controller
 */
abstract class EntityTypeListController extends ControllerBase {

  /**
   * Creates a list of entity types.
   *
   * @return array
   *   A render element.
   */
  abstract public function listing();

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
