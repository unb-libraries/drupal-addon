<?php

namespace Drupal\lib_unb_custom_entity\Entity\Storage;

/**
 * Trait to access entity storage handlers.
 *
 * @package Drupal\lib_unb_custom_entity\Entity\Storage
 */
trait EntityStorageTrait {

  /**
   * Retrieve a storage handler for the given entity type.
   *
   * @param string $entity_type_id
   *   An entity type ID string.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   An entity storage handler instance.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getStorage($entity_type_id) {
    return \Drupal::entityTypeManager()
      ->getStorage($entity_type_id);
  }

}
