<?php

namespace Drupal\lib_unb_custom_entity\Entity\Storage;

use Drupal\Core\Entity\EntityInterface;
use Drupal\lib_unb_custom_entity\Entity\HierarchicalInterface;

trait HierarchicalEntityStorageTrait {

  /**
   * {@inheritDoc}
   */
  public function loadInferiors(EntityInterface $entity, $max_desc = 1) {
    if ($max_desc < 1) {
      return [];
    }

    $query = $this
      ->getQuery()
      ->condition(HierarchicalInterface::FIELD_PARENT, $entity->id());

    $inferiors = $this->loadMultiple($query->execute());
    foreach ($inferiors as $inferior) {
      $inferiors += $this->loadInferiors($inferior, $max_desc - 1);
    }

    return $inferiors;
  }

  /**
   * {@inheritDoc}
   */
  public function loadFellows(EntityInterface $entity) {
    /** @var HierarchicalInterface $entity */
    $superior_id = $entity->getSuperior()->id();

    $query = $this
      ->getQuery()
      ->condition(HierarchicalInterface::FIELD_PARENT, $superior_id);
    return $this->loadMultiple($query->execute());
  }

}
