<?php

namespace Drupal\lib_unb_custom_entity\Entity\Storage;

use Drupal\Core\Entity\ContentEntityStorageInterface;

/**
 * Enhanced storage handler for content entities.
 *
 * @package Drupal\lib_unb_custom_entity\Entity\Storage
 */
interface CustomContentEntityStorageInterface extends ContentEntityStorageInterface, RevisionableEntityStorageInterface {

  /**
   * Load entities that have not been changed since the given time.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime|string $datetime
   *   A datetime object or string.
   * @param \DateTimeZone|string|null $timezone
   *   (optional) The timezone to which the given
   *   datetime object is set. If none is provided the
   *   storage timezone will be used.
   *
   * @return \Drupal\lib_unb_custom_entity\Entity\ContentEntityInterface[]
   *   An array of content entities.
   */
  public function loadUnalteredSince($datetime, $timezone = NULL);

  /**
   * Load entities that have not been altered for the given time period.
   *
   * @param string $duration
   *   A string indicating a time period, e.g. "3 days".
   *
   * @return \Drupal\lib_unb_custom_entity\Entity\ContentEntityInterface[]
   *   An array of content entities.
   */
  public function loadUnalteredFor($duration);

}
