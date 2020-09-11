<?php

namespace Drupal\subforms\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Interface for sub-form builder implementations.
 *
 * @package Drupal\subforms\Form
 */
interface SubFormBuilderInterface {

  /**
   * Resolve the form class into a form render array.
   *
   * @param string|\Drupal\Core\Form\FormInterface $form_arg
   *   A form object or form class name.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   A form render array.
   */
  public function retrieveForm($form_arg, FormStateInterface $form_state);

  /**
   * Retrieve the value of the form.
   *
   * @param array $form
   *   The current form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return mixed
   *   The value of the form.
   */
  public function getFormValue(array &$form, FormStateInterface $form_state);

  /**
   * Prepare the form for further processing.
   *
   * @param array $form
   *   The current form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function prepareForm(array &$form, FormStateInterface &$form_state);

  /**
   * Validate the form.
   *
   * @param array $form
   *   The current form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface &$form_state);

}
