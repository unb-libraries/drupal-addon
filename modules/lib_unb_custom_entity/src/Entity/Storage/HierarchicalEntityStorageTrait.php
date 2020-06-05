<?php

namespace Drupal\lib_nb_custom_entity\Entity;

use Drupal\Core\Entity\EntityInterface;

trait HierarchicalEntityStorageTrait {

  /**
   * {@inheritDoc}
   */
  public function loadInferiors(EntityInterface $entity, $max_desc = 1) {
    // TODO: Implement getInferiors() method.
  }

  /**
   * {@inheritDoc}
   */
  public function loadFellows(EntityInterface $entity) {
    // TODO: Implement getFellows() method.
  }

}
