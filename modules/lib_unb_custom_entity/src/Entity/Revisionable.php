<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\RevisionableInterface;

/**
 * Extended interface of Drupal's default interface for revisionable entities.
 *
 * @package Drupal\lib_unb_custom_entity\Entity\Storage
 */
interface Revisionable extends RevisionableInterface {

  /**
   * @return static[]
   */
  public function getRevisions();

}
