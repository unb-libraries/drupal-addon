<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\FieldableEntityInterface;

/**
 * Interface for entities that can be created by users.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
interface UserCreatableInterface extends FieldableEntityInterface {

  const FIELD_CREATOR = 'creator';

  /**
   * Get the creator.
   *
   * @return \Drupal\Core\Session\AccountInterface
   *   A user entity.
   */
  public function getCreator();

}
