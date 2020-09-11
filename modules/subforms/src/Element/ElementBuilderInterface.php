<?php

namespace Drupal\subforms\Element;

use Drupal\Core\Form\FormStateInterface;

/**
 * Interface for element builder implementations.
 *
 * @package Drupal\subforms\Element
 */
interface ElementBuilderInterface {

  /**
   * Prepare the given element for future processing.
   *
   * @param string $element_id
   *   The ID under which the element appears in its parent.
   * @param array $element
   *   An element render array.
   * @param array $form
   *   The complete form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function prepareElement($element_id, array &$element, array &$form, FormStateInterface $form_state);

  /**
   * Retrieve the value of the given element.
   *
   * @param array $element
   *   An element render array.
   * @param array $form
   *   The complete form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function getElementValue(array &$element, array &$form, FormStateInterface &$form_state);

}
