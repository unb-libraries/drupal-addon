<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use \Drupal\Core\Entity\ContentEntityBase as DefaultContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionLogEntityTrait;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\datetime_plus\DependencyInjection\UserTimeTrait;

/**
 * Enhances Drupal's original ContentEntityBase class.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
abstract class ContentEntityBase extends DefaultContentEntityBase {

  use RevisionLogEntityTrait;
  use UserTimeTrait;

  const FIELD_CREATED = 'created';
  const FIELD_CHANGED = 'changed';

  /**
   * Retrieve the storage handler.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   An entity storage handler object.
   */
  protected function getStorage() {
    /** @noinspection PhpUnhandledExceptionInspection */
    return $this->entityTypeManager()
      ->getStorage($this->getEntityTypeId());
  }

  /**
   * {@inheritDoc}
   */
  public function toUrl($rel = 'canonical', array $options = []) {
    /** @var \Drupal\Core\Url $url */
    $url = parent::toUrl($rel, $options);
    if (array_key_exists('format', $options) && $this->respondsTo($format = $options['format'])) {
      $url->setOption('query', [
        '_format' => $format,
      ]);
    }
    return $url;
  }

  /**
   * Whether the entity responds to requests specifying the given format.
   *
   * @param string $format
   *   The format, e.g. 'html' or 'json'.
   *
   * @return bool
   *   TRUE if a route provider exists for the given format
   *   and the entity. FALSE otherwise.
   */
  protected function respondsTo($format) {
    $route_providers = $this->getEntityType()
      ->getHandlerClasses()['route_provider'];
    return array_key_exists($format, $route_providers);
  }

  /**
   * Loads one or more entities and returns their labels.
   *
   * @param array $ids
   *   An array of entity IDs, or NULL to load all entities.
   *
   * @return static[]
   *   An array of entity labels indexed by their IDs.
   */
  public static function loadMultipleLabels(array $ids = NULL) {
    return array_map(function (ContentEntityBase $entity) {
      return $entity->label();
    }, self::loadMultiple($ids));
  }

  /**
   * Retrieve the entity's creation date and time.
   *
   * The timezone is set to the currently logged-in user's.
   *
   * @return \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   *   A datetime object.
   */
  public function getCreated() {
    return $this->userTime()
      ->createFromTimestamp($this->get(self::FIELD_CREATED)->value);
  }

  /**
   * Retrieve the entity's date and time of its most recent edit.
   *
   * The timezone is set to the currently logged-in user's.
   *
   * @return \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   *   A datetime object.
   */
  public function getChanged() {
    return $this->userTime()
      ->createFromTimestamp($this->get(self::FIELD_CHANGED)->value);
  }

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    if ($entity_type->hasKey('revision')) {
      $fields += static::revisionLogBaseFieldDefinitions($entity_type);
    }

    $fields[self::FIELD_CREATED] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t("Timestamp indicating the location's creation."));

    $fields[self::FIELD_CHANGED] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t("Timestamp indicating the location's last update."));

    return $fields;
  }

}
