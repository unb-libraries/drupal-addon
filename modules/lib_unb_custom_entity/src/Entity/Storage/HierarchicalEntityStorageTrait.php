<?php

namespace Drupal\lib_unb_custom_entity\Entity\Storage;

use Drupal\Core\Entity\EntityInterface;
use Drupal\lib_unb_custom_entity\Entity\HierarchicalInterface;

/**
 * Provides methods for loading entities in a hierarchy.
 *
 * @package Drupal\lib_unb_custom_entity\Entity\Storage
 */
trait HierarchicalEntityStorageTrait {

  /**
   * {@inheritDoc}
   */
  public function loadInferiors(EntityInterface $entity, $max_desc = HierarchicalInterface::UNLIMITED) {
    $query = $this->getQuery()
      ->condition(HierarchicalInterface::FIELD_PARENT, $entity->id());
    $inferiors = $this->loadMultiple($query->execute());
    if ($max_desc > HierarchicalInterface::IMMEDIATE || $max_desc <= HierarchicalInterface::UNLIMITED) {
      foreach ($inferiors as $inferior) {
        $inferiors += $this->loadInferiors($inferior, $max_desc - 1);
      }
    }
    return $inferiors;
  }

  /**
   * {@inheritDoc}
   */
  public function loadFellows(EntityInterface $entity) {
    /** @var \Drupal\lib_unb_custom_entity\Entity\HierarchicalInterface $entity */

    $ids = [];
    if ($superior = $entity->getSuperior()) {
      $ids = $this
        ->getQuery()
        ->condition(HierarchicalInterface::FIELD_PARENT, $superior->id())
        ->execute();
    }

    return $this->loadMultiple($ids);
  }

}
