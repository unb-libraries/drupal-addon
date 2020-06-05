<?php

namespace Drupal\lib_nb_custom_entity\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Storage handler for interface for hierarchical entity storage classes.
 *
 * @package Drupal\lib_nb_custom_entity\Entity
 */
interface HierarchicalEntityStorageInterface extends EntityStorageInterface {

  /**
   * Retrieve entities superior to the given one.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity whose superiors to retrieve.
   * @param int $max_desc
   *   Positive integer indicating how many lower hierarchy
   *   levels should be included in the result.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entity objects.
   */
  public function loadInferiors(EntityInterface $entity, $max_desc = 1);

  /**
   * Retrieve entities sharing a common immediate superior.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity whose fellows to retrieve.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   An array of entity objects.
   */
  public function loadFellows(EntityInterface $entity);

}
