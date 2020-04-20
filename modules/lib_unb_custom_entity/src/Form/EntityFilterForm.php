<?php

namespace Drupal\lib_unb_custom_entity\Form;

use Consolidation\OutputFormatters\Exception\InvalidFormatException;
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

  const ANY = 'any';
  const NONE = 'none';

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
      $form['actions'] = [
        '#type' => 'container',
      ] + $actions + [
        '#weight' => 99,
      ];
    }
    
    // Retrieve and add form attributes, such as CSS classes.
    if (!empty($attributes = $this->attributes($form, $form_state))) {
      $form['#attributes'] = array_merge_recursive($form['#attributes'], $attributes);
    }

    return $form;
  }

  /**
   * Initialize the form state and the entity before the first form build.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  protected function init(FormStateInterface $form_state) {
    $this->initFormState($form_state);
    // Flag that this form has been initialized.
    $form_state->set('entity_form_initialized', TRUE);
  }

  /**
   * Initialize the form state. Populate the form state by inheriting values from the query string.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  protected function initFormState(FormStateInterface &$form_state) {
    foreach ($this->getRequest()->query->all() as $param => $value) {
      $form_state->setValue($param, $value);
    }
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

    $actions['reset'] = [
      '#type' => 'submit',
      '#value' => $this->t('Reset'),
      '#submit' => ['::resetForm'],
    ];

    return $actions;
  }

  /**
   * Assign additional attributes to the form, e.g. CSS classes.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   Array containing attributes.
   */
  protected function attributes(array &$form, FormStateInterface $form_state) {
    return [];
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->cleanValues();
    foreach ($form as $field_id => $field) {
      if (array_key_exists('#multiple', $field) && $field['#multiple']) {
        if (!is_array($form_state->getValue($field_id))) {
          throw new InvalidFormatException("Expected array value for {$field_id}.");
        }
        $form_state->setValue($field_id, implode(';', $form_state->getValue($field_id)));
      }
    }


    $form_state->setRedirect($this->getRedirectRoute(), $form_state->getValues());
  }

  /**
   * @inheritDoc
   */
  public function resetForm(array &$form, FormStateInterface $form_state) {
    $form_state->cleanValues();
    $form_state->setRedirect($this->getRedirectRoute(), []);
  }

  /**
   * Retrieve the route to redirect to.
   *
   * @return string
   *   A route name.
   */
  protected function getRedirectRoute() {
    return $this->getRouteMatch()->getRouteName();
  }

}
