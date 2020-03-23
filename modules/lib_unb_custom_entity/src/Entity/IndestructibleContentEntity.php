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
      ->setRequired(FALSE);

    return $fields;
  }

}