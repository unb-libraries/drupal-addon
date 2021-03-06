<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\datetime_plus\Datetime\DrupalDateTimePlus;
use Drupal\datetime_plus\DependencyInjection\UserTimeTrait;

/**
 * Implements an entity that can never be deleted.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
class IndestructibleContentEntity extends ContentEntityBase {

  use UserTimeTrait;

  const FIELD_DELETED = 'deleted';

  /**
   * Marks an entity as deleted.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function delete() {
    if ($this->doesExist()) {
      $this->set(self::FIELD_DELETED, $this->userTime()->now()->getTimestamp());
      $this->save();
    }
  }

  /**
   * Assigns the given value to the property with the given name.
   *
   * Only properties of non-deleted entities can be changed.
   *
   * {@inheritDoc}
   *
   * @throws \LogicException
   */
  public function set($name, $value, $notify = TRUE) {
    if ($this->doesExist()) {
      return parent::set($name, $value, $notify);
    }
    else {
      throw new \LogicException('Deleted entities cannot be edited.');
    }
  }

  /**
   * Whether this entity exists or has been deleted.
   *
   * @return bool
   *   TRUE if it still exists. FALSE if
   *   the entity has been deleted.
   */
  public function doesExist() {
    return $this->getDeleted() ? FALSE : TRUE;
  }

  /**
   * Whether the given datetime object lies within the entity's lifetime.
   *
   * @param \Drupal\datetime_plus\Datetime\DrupalDateTimePlus $datetime
   *   The datetime object.
   *
   * @return bool
   *   TRUE if the given datetime object points at a time past the
   *   entity's creation and before the entity's deletion (or the
   *   current time, if the entity has not been deleted).
   *   FALSE if the given datetime object points at a time before
   *   the entity's creation.
   *
   * @throws \LogicException
   *   If the given datetime points at a time in the future.
   */
  public function existedOn(DrupalDateTimePlus $datetime) {
    $now = $this->userTime()->now();
    if ($datetime > $now) {
      throw new \LogicException("Can't predict the future.");
    }

    $created = $this->getCreated();
    $deleted = $this->doesExist()
      ? $now
      : $this->getDeleted();
    $lifetime = $this->userTime()
      ->createDateInterval($created, $deleted);

    return $datetime->isWithin($lifetime);
  }

  /**
   * Retrieve the entity's deletion date and time.
   *
   * The timezone is set to the currently logged-in user's.
   *
   * @return \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   *   A datetime object.
   */
  public function getDeleted() {
    if ($deleted_timestamp = $this->get(self::FIELD_DELETED)->value) {
      return $this->userTime()->createFromTimestamp($deleted_timestamp);
    }
    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields[self::FIELD_DELETED] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Deleted'))
      ->setDescription(t("Timestamp indicating the location's creation."))
      ->setRequired(FALSE)
      ->setRevisionable(TRUE);

    return $fields;
  }

}
