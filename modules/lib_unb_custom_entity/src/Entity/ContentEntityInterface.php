<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\ContentEntityInterface as DefaultContentEntityInterface;

/**
 * Interface for content entity implementations.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
interface ContentEntityInterface extends DefaultContentEntityInterface, Revisionable {

  const CREATED = 'created';
  const CHANGED = 'changed';

  /**
   * Retrieve the entity's creation datetime, set to the currently logged-in user's timezone.
   *
   * @return \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   *   A datetime object.
   */
  public function getCreated();

  /**
   * Retrieve the entity's creation timestamp.
   *
   * @return int
   *   A UNIX timestamp.
   */
  public function getCreatedTimestamp();

  /**
   * Retrieve the datetime of the most recent edit, set to the currently logged-in user's timezone.
   *
   * @return \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   *   A datetime object.
   */
  public function getChanged();

  /**
   * Retrieve the timestamp of the most recent edit.
   *
   * @return int
   *   A UNIX timestamp.
   */
  public function getChangedTimestamp();

}
