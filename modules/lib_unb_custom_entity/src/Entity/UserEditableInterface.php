<?php

namespace Drupal\lib_unb_custom_entity\Entity;

/**
 * Interface for user editable entities.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
interface UserEditableInterface {

  const FIELD_EDITOR = 'editor';

  /**
   * Get the editor.
   *
   * @return \Drupal\Core\Session\AccountInterface
   *   A user entity.
   */
  public function getEditor();

}
