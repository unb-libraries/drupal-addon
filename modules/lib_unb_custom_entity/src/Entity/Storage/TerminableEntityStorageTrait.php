<?php

namespace Drupal\lib_unb_custom_entity\Entity\Storage;

use Drupal\lib_unb_custom_entity\Entity\TerminableInterface;

/**
 * Trait to enable storage handlers to handle retrieving persistent entities.
 *
 * @package Drupal\lib_nb_custom_entity\Entity
 */
trait TerminableEntityStorageTrait {

  /**
   * {@inheritDoc}
   */
  public function loadTerminated() {
    $query = $this
      ->getQuery()->accessCheck(TRUE)
      ->exists(TerminableInterface::FIELD_DELETED);
    return $this->loadMultiple($query->execute());
  }

  /**
   * {@inheritDoc}
   */
  public function loadCurrent() {
    $query = $this
      ->getQuery()->accessCheck(TRUE)
      ->notExists(TerminableInterface::FIELD_DELETED);
    return $this->loadMultiple($query->execute());
  }

}
