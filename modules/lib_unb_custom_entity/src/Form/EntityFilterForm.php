<?php

namespace Drupal\lib_unb_custom_entity\Form;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Form implementation to filter lists of entities.
 *
 * @package Drupal\lib_unb_custom_entity\Form
 */
class EntityFilterForm extends FormBase {

  /**
   * The entity type.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface
   */
  protected $entityType;

  /**
   * Retrieve the entity type.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   The entity type definition.
   */
  public function getEntityType() {
    return $this->entityType;
  }

  /**
   * Create a new EntityFilterForm instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   */
  public function __construct(EntityTypeInterface $entity_type) {
    $this->entityType = $entity_type;
  }

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return $this->getEntityType()->id() . '__filter_form';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // During the initial form build, add this form object to the form state and
    // allow for initial preparation before form building and processing.
    if (!$form_state->has('entity_form_initialized')) {
      $this->init($form_state);
    }

    // Retrieve and add the form actions array.
    if (!empty($actions = $this->actionsElement($form, $form_state))) {
      $form['actions'] = $actions + [
        '#weight' => 99,
      ];
    }
    return $form;
  }

  protected function init(FormStateInterface $form_state) {
    // Flag that this form has been initialized.
    $form_state->set('entity_form_initialized', TRUE);
  }

  /**
   * Returns the action form element for the current entity form.
   *
   * @see \Drupal\Core\Entity\EntityForm::actionsElement()
   */
  protected function actionsElement(array $form, FormStateInterface $form_state) {
    return $this->actions($form, $form_state);
  }

  /**
   * Returns an array of supported actions for the current entity form.
   *
   * This function generates a list of Form API elements which represent
   * actions supported by the current form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   An array of supported Form API action elements keyed by name.
   *
   * @see \Drupal\Core\Entity\EntityForm::actions()
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions['filter'] = [
      '#type' => 'submit',
      '#value' => $this->t('Filter'),
      '#submit' => ['::submitForm'],
    ];
    return $actions;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $redirect_route_name = Url::createFromRequest($this
      ->getRequest())
      ->getRouteName();

    $form_state->cleanValues();
    $form_state->setRedirect($redirect_route_name, $form_state->getValues());
  }

}
