<?php

namespace Drupal\custom_entity_ui\Form;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for entity type settings forms.
 *
 * @package Drupal\custom_entity_ui\Form
 */
class EntityTypeSettingsForm extends FormBase implements EntityTypeSettingsFormInterface {

  /**
   * The entity type.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface
   */
  protected $entityType;

  /**
   * Get the entity type.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   An entity type.
   */
  protected function getEntityType() {
    return $this->entityType;
  }

  /**
   * EntityTypeSettingsForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   */
  public function __construct(EntityTypeInterface $entity_type) {
    $this->entityType = $entity_type;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    $route_matcher = $container->get('current_route_match');
    $entity_type_id = $route_matcher
      ->getRouteObject()
      ->getDefault('entity_type_id');
    $entity_type = $container->get('entity_type.manager')
      ->getDefinition($entity_type_id);

    return new static($entity_type);
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    $entity_type_id = $this->getEntityType()->id();
    return "{$entity_type_id}.settings_form";
  }

  /**
   * {@inheritDoc}
   */
  public function getTitle() {
    return $this->t('@entity_type Settings', [
      '@entity_type' => $this->getEntityType()->getLabel(),
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }


}
