<?php

namespace Drupal\lib_unb_custom_entity\Entity\Storage;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\datetime_plus\Datetime\Timespan;
use Drupal\datetime_plus\DependencyInjection\StorageTimeTrait;
use Drupal\lib_unb_custom_entity\Entity\ContentEntityInterface;

/**
 * Enhanced content entity storage handler.
 *
 * @package Drupal\lib_unb_custom_entity\Entity\Storage
 */
class CustomSqlContentEntityStorage extends SqlContentEntityStorage implements CustomContentEntityStorageInterface {

  use StorageTimeTrait;
  use RevisionableEntityStorageTrait;

  /**
   * {@inheritDoc}
   */
  public function loadUnalteredSince($datetime, $timezone = NULL) {
    if (!$timezone) {
      $timezone = DateTimeItemInterface::STORAGE_TIMEZONE;
    }
    if (is_string($datetime)) {
      $datetime = new DrupalDateTime($datetime, $timezone);
    }
    $datetime->setTimezone($this->storageTime()->getTimeZone());

    $entity_ids = $this->getQuery()
      ->condition(ContentEntityInterface::CHANGED, $datetime->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>=')
      ->execute();
    if (!empty($entity_ids)) {
      return $this->loadMultiple($entity_ids);
    }
    return [];
  }

  /**
   * {@inheritDoc}
   */
  public function loadUnalteredFor($duration) {
    $datetime = $this->storageTime()
      ->now()
      ->minus(Timespan::createFromDateString($duration));
    return $this->loadUnalteredSince($datetime);
  }

}
