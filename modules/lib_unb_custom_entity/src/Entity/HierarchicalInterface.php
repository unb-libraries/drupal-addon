<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityInterface;

/**
 * Interface for entities that shape a hierarchical relationship with each other.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
interface HierarchicalInterface extends EntityInterface {

  const FIELD_PARENT = 'parent';

  /**
   * Retrieve the higher-ranking entity of the same type.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   An entity object.
   */
  public function getSuperior();

  /**
   * Retrieve the higher-ranking entities of the same type.
   *
   * @param int $max_asc
   *   Positive integer indicating how many upper hierarchy
   *   levels should be included in the result.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entity objects.
   */
  public function getSuperiors($max_asc = 1);

  /**
   * Retrieve lower-ranking entities of the same type.
   *
   * @param int $max_desc
   *   Positive integer indicating how many lower hierarchy
   *   level should be included in the result.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entity objects.
   */
  public function getInferiors($max_desc = 1);

  /**
   * Retrieve any entities with the same parent.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entity objects.
   */
  public function getFellows();

}
