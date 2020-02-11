<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use \Drupal\Core\Entity\ContentEntityBase as DefaultContentEntityBase;

/**
 * Enhances Drupal's original ContentEntityBase class.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
abstract class ContentEntityBase extends DefaultContentEntityBase {

  /**
   * Loads one or more entities and returns their labels.
   *
   * @param array $ids
   *   An array of entity IDs, or NULL to load all entities.
   *
   * @return static[]
   *   An array of entity labels indexed by their IDs.
   */
  public static function loadMultipleLabels(array $ids = NULL) {
    return array_map(function (ContentEntityBase $entity) {
      return $entity->label();
    }, self::loadMultiple($ids));
  }

}
