<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\RevisionableInterface as DefaultRevisionableInterface;

/**
 * Extended interface of Drupal's default interface for revisionable entities.
 *
 * @package Drupal\lib_unb_custom_entity\Entity\Storage
 */
interface RevisionableInterface extends DefaultRevisionableInterface {

  /**
   * Retrieve all revisions.
   *
   * @return static[]
   *   An array of entity revisions, keyed by each entity's revision ID.
   */
  public function getRevisions();

}
