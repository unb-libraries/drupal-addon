<?php

namespace Drupal\custom_entity_mail\EventSubscriber;

use Drupal\Core\Mail\MailManagerInterface;
use Drupal\lib_unb_custom_entity\Event\EntityEvent;
use Drupal\lib_unb_custom_entity\EventSubscriber\EntityEventSubscriber;

/**
 * Event subscriber sending emails upon entity events.
 */
abstract class EntityEventMailer extends EntityEventSubscriber {

  /**
   * The mail manager service.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * Get the mail manager service.
   *
   * @return \Drupal\Core\Mail\MailManagerInterface
   *   A mail manager instance.
   */
  protected function mailManager() {
    return $this->mailManager;
  }

  /**
   * Construct an EntityMailEventSubscriber.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   A mail manager instance.
   * @param string $entity_type_id
   *   A string.
   */
  public function __construct(MailManagerInterface $mail_manager, string $entity_type_id) {
    parent::__construct($entity_type_id);
    $this->mailManager = $mail_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function doOnCreate(EntityEvent $event) {
    $entity = $event->getEntity();
    $key = "{$entity->getEntityTypeId()}.created";
    $recipients = $this->getRecipients($event);
    $subject = "{$entity->label()} has been created.";
    $message = "{$entity->label()} has been created.";
    $this->mail($event, $key, $recipients, $subject, $message);
  }

  /**
   * {@inheritDoc}
   */
  public function doOnUpdate(EntityEvent $event) {
    $entity = $event->getEntity();
    $key = "{$entity->getEntityTypeId()}.updated";
    $recipients = $this->getRecipients($event);
    $subject = "{$entity->label()} has been updated.";
    $message = "{$entity->label()} has been updated.";
    $this->mail($event, $key, $recipients, $subject, $message);
  }

  /**
   * {@inheritDoc}
   */
  public function doOnDelete(EntityEvent $event) {
    $entity = $event->getEntity();
    $key = "{$entity->getEntityTypeId()}.deleted";
    $recipients = $this->getRecipients($event);
    $subject = "{$entity->label()} has been deleted.";
    $message = "{$entity->label()} has been deleted.";
    $this->mail($event, $key, $recipients, $subject, $message);
  }

  /**
   * Get the recipients which should be notified.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   The event.
   *
   * @return array
   *   An array of email address strings.
   */
  abstract protected function getRecipients(EntityEvent $event);

  /**
   * Send the mail.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent $event
   *   The entity event.
   * @param string $key
   *   A key.
   * @param array $recipients
   *   An array of email addresses.
   * @param string $subject
   *   The email subject.
   * @param string $message
   *   The email body.
   */
  protected function mail(EntityEvent $event, string $key, array $recipients, string $subject, string $message) {
    $module = $this->getModule($event);
    $lang_code = $event->getEntity()
      ->get('langcode')->value;
    foreach ($recipients as $recipient_email) {
      $this->mailManager()->mail($module, $key, $recipient_email, $lang_code, [
        'subject' => $subject,
        'message' => $message,
      ]);
    }
  }

  /**
   * Get the module that should handle sending the mail.
   *
   * @param \Drupal\lib_unb_custom_entity\Event\EntityEvent|null $event
   *   (optional) The event upon which mail should be sent.
   *
   * @return string
   *   A module name string.
   */
  protected function getModule(EntityEvent $event = NULL) {
    return 'custom_entity_mail';
  }

}
