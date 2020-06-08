<?php

namespace Drupal\lib_nb_custom_entity\Entity;

use Drupal\lib_unb_custom_entity\Entity\PersistentInterface;

/**
 * Trait to enable storage handlers to handle retrieving persistent entities.
 *
 * @package Drupal\lib_nb_custom_entity\Entity
 */
trait PersistentEntityStorageTrait {

  /**
   * {@inheritDoc}
   */
  public function loadTerminated() {
    $query = $this
      ->getQuery()
      ->exists(PersistentInterface::FIELD_DELETED);
    return $this->loadMultiple($query->execute());
  }

  /**
   * {@inheritDoc}
   */
  public function loadCurrent() {
    $query = $this
      ->getQuery()
      ->notExists(PersistentInterface::FIELD_DELETED);
    return $this->loadMultiple($query->execute());
  }

}
