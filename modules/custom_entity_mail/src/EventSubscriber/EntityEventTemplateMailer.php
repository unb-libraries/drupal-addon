<?php

namespace Drupal\custom_entity_mail\EventSubscriber;

use Drupal\Core\Entity\EntityInterface;

/**
 * Event subscriber sending template-based emails upon entity events.
 *
 * @package Drupal\custom_entity_mail\EventSubscriber
 */
abstract class EntityEventTemplateMailer extends EntityEventMailer {

  /**
   * {@inheritDoc}
   */
  protected function getSubject(EntityInterface $entity, string $key) {
    if ($template_path = $this->getSubjectTemplate($entity, $key)) {
      return [
        'template' => $template_path,
        'context' => $this->getSubjectContext($entity, $key),
      ];
    }
    return "";
  }

  /**
   * Find a subject template for the given entity and key.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $key
   *   The message key.
   *
   * @return string|false
   *   A path to an existing template file, excluding potential file extensions
   *   from a template engine. FALSE if no suitable template can be found.
   */
  protected function getSubjectTemplate(EntityInterface $entity, string $key) {
    $provider = $entity->getEntityType()->getProvider();
    $path = drupal_get_path('module', $provider) . "/templates/{$key}.subject";
    if (!empty(glob("$path*"))) {
      return $path;
    }
    return FALSE;
  }

  /**
   * The context to pass to a renderer when rendering the subject template.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $key
   *   The message key.
   *
   * @return array
   *   An array of variable names and values.
   */
  protected function getSubjectContext(EntityInterface $entity, string $key) {
    return [
      $entity->getEntityTypeId() => $entity,
    ];
  }

  /**
   * {@inheritDoc}
   */
  protected function getBody(EntityInterface $entity, string $key) {
    if ($template_path = $this->getBodyTemplate($entity, $key)) {
      return [
        'template' => $template_path,
        'context' => $this->getBodyContext($entity, $key),
      ];
    }
    return "";
  }

  /**
   * Find a body template for the given entity and key.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $key
   *   The message key.
   *
   * @return string|false
   *   A path to an existing template file, excluding potential file extensions
   *   from a template engine. FALSE if no suitable template can be found.
   */
  protected function getBodyTemplate(EntityInterface $entity, string $key) {
    $provider = $entity->getEntityType()->getProvider();
    $path = drupal_get_path('module', $provider) . "/templates/{$key}.body";
    if (!empty(glob("$path*"))) {
      return $path;
    }
    return FALSE;
  }

  /**
   * The context to pass to a renderer when rendering the body template.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $key
   *   The message key.
   *
   * @return array
   *   An array of variable names and values.
   */
  protected function getBodyContext(EntityInterface $entity, string $key) {
    return [
      $entity->getEntityTypeId() => $entity,
    ];
  }

}
