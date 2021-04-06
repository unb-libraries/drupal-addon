<?php

namespace Drupal\custom_entity_mail\EventSubscriber;

use Drupal\Core\Entity\EntityInterface;
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
  public function doOnEntityCreate(EntityEvent $event) {
    $entity = $event->getEntity();
    $key = "{$entity->getEntityTypeId()}.created";
    $this->mail(
      $event,
      $key,
      $this->getRecipients($event),
      $this->getSubject($entity, $key),
      $this->getBody($entity, $key)
    );
  }

  /**
   * {@inheritDoc}
   */
  public function doOnEntityUpdate(EntityEvent $event) {
    $entity = $event->getEntity();
    $key = "{$entity->getEntityTypeId()}.updated";
    $this->mail(
      $event,
      $key,
      $this->getRecipients($event),
      $this->getSubject($entity, $key),
      $this->getBody($entity, $key)
    );
  }

  /**
   * {@inheritDoc}
   */
  public function doOnEntityDelete(EntityEvent $event) {
    $entity = $event->getEntity();
    $key = "{$entity->getEntityTypeId()}.deleted";
    $this->mail(
      $event,
      $key,
      $this->getRecipients($event),
      $this->getSubject($entity, $key),
      $this->getBody($entity, $key)
    );
  }

  /**
   * Build the subject content or definition.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $template_key
   *   The key to determine a template name.
   *
   * @return false|array|string
   *   An array containing a template path and context, if a suitable context
   *   for the given entity and key is available. FALSE if no template can be
   *   found.
   *
   *   Example:
   *   For an entity of type "node" and a template key "created", a suitable
   *   template must be located in the "/templates" folder of the module
   *   defining the "node" entity type. The template name must match the form
   *   "node.created.subject*".
   */
  protected function getSubject(EntityInterface $entity, string $template_key) {
    $provider = $entity->getEntityType()->getProvider();
    $path = drupal_get_path('module', $provider) . "/templates/{$template_key}.subject";
    if (!empty(glob("$path*"))) {
      return [
        'template' => $path,
        'context' => [
          $entity->getEntityTypeId() => $entity,
        ],
      ];
    }
    return FALSE;
  }

  /**
   * Build the body content or definition.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $key
   *   The key to determine a template name.
   *
   * @return false|array|string
   *   An array containing a template path and context, if a suitable context
   *   for the given entity and key is available. FALSE if no template can be
   *   found.
   *
   *   Example:
   *   For an entity of type "node" and a template key "created", a suitable
   *   template must be located in the "/templates" folder of the module
   *   defining the "node" entity type. The template name must match the form
   *   "node.created.body*".
   */
  protected function getBody(EntityInterface $entity, string $key) {
    $provider = $entity->getEntityType()->getProvider();
    $path = drupal_get_path('module', $provider) . "/templates/{$key}.body";
    if (!empty(glob("$path*"))) {
      return [
        'template' => $path,
        'context' => [
          $entity->getEntityTypeId() => $entity,
        ],
      ];
    }
    return FALSE;
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
   * @param string $body
   *   The email body.
   */
    $module = $this->getModule($event);
  protected function mail(EntityEvent $event, string $key, array $recipients, string $subject = '', string $body = '') {
    $lang_code = $event->getEntity()
      ->get('langcode')->value;
    foreach ($recipients as $recipient_email) {
      $this->mailManager()->mail($module, $key, $recipient_email, $lang_code, [
        'subject' => $subject,
        'body' => $body,
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
