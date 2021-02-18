<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityInterface;

/**
 * Interface for hierarchical entities.
 *
 * A hierarchical entity is one that shapes superior-inferior (aka
 * parent-child) relationships with other entities of the same type.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
interface HierarchicalInterface extends EntityInterface {

  const FIELD_PARENT = 'parent';
  const UNLIMITED = -1;
  const IMMEDIATE = 1;

  /**
   * Retrieve the higher-ranking entity of the same type.
   *
   * @return static
   *   An entity object.
   */
  public function getSuperior();

  /**
   * Set a higher-ranking entity as this entity's superior.
   *
   * @param static|null $entity
   *   The entity to assign.
   */
  public function setSuperior($entity);

  /**
   * Retrieve the higher-ranking entities of the same type.
   *
   * @param int $max_asc
   *   Positive integer indicating how many upper hierarchy
   *   levels should be included in the result.
   *
   * @return static[]
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
   * @return static[]
   *   An array of entity objects.
   */
  public function getInferiors($max_desc = 1);

  /**
   * Retrieve any entities with the same parent.
   *
   * @return static[]
   *   An array of entity objects.
   */
  public function getFellows();

}
