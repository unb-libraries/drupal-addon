<?php

namespace Drupal\subforms\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityFormInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

/**
 * Provides form building and processing of integrated sub-forms.
 *
 * @package Drupal\subforms\Form
 */
class SubFormBuilder implements SubFormBuilderInterface {

  /**
   * The class resolver service.
   *
   * @var \Drupal\Core\DependencyInjection\ClassResolverInterface
   */
  protected $_classResolver;

  /**
   * The element info manager service.
   *
   * @var \Drupal\Core\Render\ElementInfoManagerInterface
   */
  protected $_elementInfoManager;

  /**
   * Retrieve the class resolver service.
   *
   * @return \Drupal\Core\DependencyInjection\ClassResolverInterface
   *   A class resolver instance.
   */
  protected function classResolver() {
    if (!isset($this->_classResolver)) {
      $this->_classResolver = \Drupal::classResolver();
    }
    return $this->_classResolver;
  }

  /**
   * Retrieve the element info manager service.
   *
   * @return \Drupal\Core\Render\ElementInfoManagerInterface
   *   An element info manager instance.
   */
  protected function elementInfoManager() {
    if (!isset($this->_elementInfoManager)) {
      $this->_elementInfoManager = \Drupal::service('plugin.manager.element_info');
    }
    return $this->_elementInfoManager;
  }

  /**
   * {@inheritDoc}
   */
  public function retrieveForm($form_arg, FormStateInterface $form_state) {
    if (is_string($form_arg) && class_exists($form_arg)) {
      $form_arg = $this->classResolver()
        ->getInstanceFromDefinition($form_arg);
    }

    if (!$this->isValidForm($form_arg)) {
      throw new \InvalidArgumentException("The form argument $form_arg is not a valid form.");
    }

    /** @var FormInterface $form_obj */
    $form_obj = $form_arg;
    $form_state->setFormObject($form_obj);
    $form = $form_arg->buildForm(['#type' => 'container'], $form_state);

    if (array_key_exists('actions', $form)) {
      unset($form['actions']);
    }

    return $form;
  }

  /**
   * Whether the form argument represents a valid form object.
   *
   * @param mixed $form_arg
   *   An object.
   *
   * @return bool
   *   TRUE if the given argument is a valid form. FALSE otherwise.
   */
  protected function isValidForm($form_arg) {
    return is_object($form_arg) && $form_arg instanceof EntityFormInterface;
  }

  /**
   * {@inheritDoc}
   */
  public function getFormValue(array &$form, FormStateInterface $form_state) {
    $form_state->setValues([]);
    foreach (Element::children($form) as $field_id) {
      $this->getElementValue($form[$field_id], $form, $form_state);
    }

    /** @var \Drupal\Core\Entity\EntityFormInterface $form_obj */
    $form_obj = $form_state->getFormObject();

    return $form_obj->buildEntity($form, $form_state);
  }

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
  protected function getElementValue(array &$element, array &$form, FormStateInterface &$form_state) {
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
   * {@inheritDoc}
   */
  public function prepareForm(&$form, FormStateInterface &$form_state) {
    foreach (Element::children($form) as $field_id) {
      $this->prepareElement($field_id, $form[$field_id], $form, $form_state);
    }
  }

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
  protected function prepareElement($element_id, array &$element, array &$form, FormStateInterface $form_state) {
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
  public function validateForm(&$form, FormStateInterface &$form_state) {
    // TODO: Implement validateForm() method.
  }

}
