<?php

namespace Drupal\lib_unb_custom_entity\EventSubscriber;

use Drupal\lib_unb_custom_entity\Event\EntityEvent;
use Drupal\lib_unb_custom_entity\Event\EntityEvents;

/**
 * Base class for entity event subscriber implementations.
 *
 * @package Drupal\lib_unb_custom_entity\Entity\EventSubscriber
 */
abstract class EntityEventSubscriber implements EntityEventSubscriberInterface {

  /**
   * The entity type the subscriber should process.
   *
   * @var string
   */
  protected $entityTypeId;

  /**
   * {@inheritDoc}
   */
  public function getEntityTypeId() {
    if (isset($this->entityTypeId)) {
      return $this->entityTypeId;
    }
    return FALSE;
  }

  /**
   * Create a new entity event subscriber instance.
   *
   * @param string|false $entity_type_id
   *   An entity type ID.
   */
  public function __construct($entity_type_id = FALSE) {
    if ($entity_type_id) {
      $this->entityTypeId = $entity_type_id;
    }
  }

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    return [
      EntityEvents::SAVE => 'onSave',
      EntityEvents::CREATE => 'onCreate',
      EntityEvents::UPDATE => 'onUpdate',
      EntityEvents::DELETE => 'onDelete',
    ];
  }

  /**
   * Whether the subscriber handles the passed event.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   The event.
   *
   * @return bool
   *   TRUE if the passed event was triggered by an entity of
   *   a type to which the handler subscribes. FALSE otherwise.
   */
  public function doesHandle(EntityEvent $event) {
    return !$this->getEntityTypeId()
      || $this->getEntityTypeId() === $event->getEntity()->getEntityTypeId();
  }

  /**
   * Process a SAVE event.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   An entity event object.
   */
  final public function onSave(EntityEvent $event) {
    if ($this->doesHandle($event)) {
      $this->doOnSave($event);
    }
  }

  /**
   * The actual processing of a SAVE event.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   An entity event object.
   */
  public function doOnSave(EntityEvent $event) {}

  /**
   * Process a CREATE event.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   An entity event object.
   */
  final public function onCreate(EntityEvent $event) {
    if ($this->doesHandle($event)) {
      $this->doOnCreate($event);
    }
  }

  /**
   * The actual processing of a CREATE event.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   An entity event object.
   */
  public function doOnCreate(EntityEvent $event) {}

  /**
   * Process an UPDATE event.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   An entity event object.
   */
  final public function onUpdate(EntityEvent $event) {
    if ($this->doesHandle($event)) {
      $this->doOnUpdate($event);
    }
  }

  /**
   * Process a DELETE event.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   The entity event object.
   */
  final public function onDelete(EntityEvent $event) {
    if ($this->doesHandle($event)) {
      $this->doOnDelete($event);
    }
  }

  /**
   * The actual processing of an UPDATE event.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   An entity event object.
   */
  public function doOnUpdate(EntityEvent $event) {}

  /**
   * The actual processing of a DELETE event.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   An entity event object.
   */
  public function doOnDelete(EntityEvent $event) {}

}
