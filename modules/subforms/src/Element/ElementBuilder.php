<?php

namespace Drupal\subforms\Element;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\ElementInfoManagerInterface;

/**
 * Provides element building and processing.
 *
 * @package Drupal\subforms\Element
 */
class ElementBuilder implements ElementBuilderInterface {

  /**
   * The element info manager service.
   *
   * @var \Drupal\Core\Render\ElementInfoManagerInterface
   */
  protected $_elementInfoManager;

  /**
   * Retrieve the element info manager service.
   *
   * @return \Drupal\Core\Render\ElementInfoManagerInterface
   *   An element info manager instance.
   */
  protected function elementInfoManager() {
    return $this->_elementInfoManager;
  }

  /**
   * Create a new ElementBuilder instance.
   *
   * @param \Drupal\Core\Render\ElementInfoManagerInterface $element_info_manager
   *   The element info manager service.
   */
  public function __construct(ElementInfoManagerInterface $element_info_manager) {
    $this->_elementInfoManager = $element_info_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function prepareElement($element_id, array &$element, array &$form, FormStateInterface $form_state) {
    if (isset($element['#type'])) {
      $element += $this->elementInfoManager()
        ->getInfo($element['#type']);
    }
    $this->setParents($element_id, $element, $form, $form_state);
    foreach (Element::children($element) as $child_id) {
      if ($element['#tree']) {
        $element[$child_id]['#parents'] = $element['#parents'];
      }
      $this->prepareElement($child_id, $element[$child_id], $form, $form_state);
    }
  }

  /**
   * Determine and set the given element's parents.
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
  protected function setParents($element_id, array &$element, array &$form, FormStateInterface &$form_state) {
    if (!isset($element['#tree'])) {
      $element['#tree'] = FALSE;
    }
    if (!isset($element['#parents'])) {
      $element['#parents'] = $form['#parents'];
    }
    $element['#parents'] = array_merge($element['#parents'], [$element_id]);
  }

  /**
   * {@inheritDoc}
   */
  public function getElementValue(array &$element, array &$form, FormStateInterface &$form_state) {
    if (isset($element['#input']) && $element['#input']) {
      $input_exists = NULL;

      // As the form state only contains the user input of the subform,
      // we must also only consider parent elements that lie within the subform
      // when extracting values from the user input array.
      $parents = array_diff($element['#parents'], $form['#parents']);
      $input = NestedArray::getValue($form_state->getUserInput(), $parents, $input_exists);

      if ($input) {
        $value_callback = $this->getValueCallback($element);
        $value = call_user_func_array($value_callback, [&$element, $input, $form_state]);

        // TODO: What if value is a single-value array (e.g. entity_reference_revisions)?
        if (is_array($value)) {
          $value = array_values($value);
        }

        $form_state->setValue($parents, $value);
      }
    }
    else {
      foreach (Element::children($element) as $child_id) {
        $this->getElementValue($element[$child_id], $form, $form_state);
      }
    }
  }

  /**
   * Retrieve the value callback for the given element.
   *
   * @param array $element
   *   An element render array.
   *
   * @return callable
   *   A callback.
   */
  protected function getValueCallback(array $element) {
    if (isset($element['#value_callback']) && is_callable($value_callback = $element['#value_callback'])) {
      return $value_callback;
    }
    return $value_callback = [Element\FormElement::class, 'valueCallback'];
  }

  /**
   * Whether the given element is required.
   *
   * @param array $element
   *   The element.
   *
   * @return bool
   *   TRUE if the given element or any of its children
   *   sets #required = TRUE. FALSE otherwise.
   */
  public function isRequired(array $element) {
    return isset($element['#required']) && $element['#required'];
  }

}
