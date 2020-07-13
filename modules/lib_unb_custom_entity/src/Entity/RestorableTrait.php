<?php

namespace Drupal\lib_unb_custom_entity\Entity;

/**
 * Trait to make terminable entities restorable.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
trait RestorableTrait {

  /**
   *  {@inheritDoc}
   */
  public function restore() {
    return $this->set(TerminableInterface::FIELD_DELETED, NULL)
      ->save();
  }

}
