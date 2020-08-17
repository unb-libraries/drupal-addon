<?php

namespace Drupal\lib_unb_custom_entity\EventSubscriber;

use Drupal\lib_unb_custom_entity\Event\EntityEvent;
use Drupal\lib_unb_custom_entity\Event\EntityEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Base class for entity event subscriber implementations.
 *
 * @package Drupal\lib_unb_custom_entity\Entity\EventSubscriber
 */
abstract class EntityEventSubscriber implements EventSubscriberInterface {

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
   * Process a SAVE event.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   An entity event object.
   */
  final public function onSave(EntityEvent $event) {
  }

  /**
   * Process a CREATE event.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   An entity event object.
   */
  final public function onCreate(EntityEvent $event) {
  }

  /**
   * Process an UPDATE event.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   An entity event object.
   */
  final public function onUpdate(EntityEvent $event) {
  }

  /**
   * Process a DELETE event.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   The entity event object.
   */
  final public function onDelete(EntityEvent $event) {
  }

}
