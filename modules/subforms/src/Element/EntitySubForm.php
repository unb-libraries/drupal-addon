<?php

namespace Drupal\subforms\Element;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\CompositeFormElementTrait;
use Drupal\Core\Render\Element\Container;
use Drupal\Core\Render\Element\FormElement;
use Drupal\Component\Utility\Html as HtmlUtility;

/**
 * Renders a form "select" element containing entities of a given type as its options.
 *
 * Properties:
 *   - #entity_type: (string) ID of the entity type an instance of which will be the subject of the form.
 *   - #operation: (string) Which type of form to build.
 *
 * Usage example:
 * @code
 * $form['entity'] = [
 *   '#type' => 'entity_subform',
 *   '#title' => $this->t('Entity'),
 *   '#entity_type' => 'node',
 *   '#operation' => 'add'
 * ];
 * @endcode
 *
 * @FormElement("entity_subform")
 */
class EntitySubForm extends FormElement {

  use CompositeFormElementTrait;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected static $entityTypeManager;

  /**
   * The entity form builder service.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilderInterface
   */
  protected static $entityFormBuilder;

  /**
   * Retrieve an entity type manager service instance.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   An entity type manager.
   */
  protected static function entityTypeManager() {
    if (!isset(static::$entityTypeManager)) {
      static::$entityTypeManager = \Drupal::entityTypeManager();
    }
    return static::$entityTypeManager;
  }

  /**
   * Retrieve a form builder service instance.
   *
   * @return \Drupal\Core\Entity\EntityFormBuilderInterface
   *   A form builder.
   */
  protected static function entityFormBuilder() {
    if (!isset(static::$entityFormBuilder)) {
      static::$entityFormBuilder = \Drupal::service('entity.form_builder');
    }
    return static::$entityFormBuilder;
  }

  /**
   * {@inheritDoc}
   */
  public function getInfo() {
    return [
      '#entity_type' => '',
      '#input' => TRUE,
      '#operation' => 'default',
      '#pre_render' => [
        [static::class, 'preRenderCompositeFormElement'],
        [static::class, 'preRenderGroup'],
      ],
      '#process' => [
        [static::class, 'processContainerOrFieldset'],
        [static::class, 'processBuildForm'],
        [static::class, 'processParents'],
        [static::class, 'processConditionallyRequiredStates'],
        [static::class, 'processGroup'],
      ],
      '#element_validate' => [
        [static::class, 'validateSubForm'],
      ],
    ];
  }

  /**
   * Form element processing handler.
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @return array
   *   The processed element.
   */
  public static function processContainerOrFieldset(&$element, FormStateInterface $form_state, array &$complete_form) {
    $element['#tree'] = TRUE;
    // Let sub-form determine which elements are required or optional.
    if (isset($element['#required'])) {
      unset($element['#required']);
    }

    if (array_key_exists('#title', $element)) {
      $element['#process'][] = [static::class, 'processAjaxForm'];
      $element['#theme_wrappers'][] = 'fieldset';
    }
    else {
      $element['#pre_render'][] = [Container::class, 'preRenderContainer'];
      $element['#theme_wrappers'][] = 'container';
      Container::processContainer($element, $form_state, $complete_form);
    }
    return $element;
  }

  /**
  * {@inheritdoc}
  */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    if (!$input || !isset($element['#default_value'])) {
      if (!$input) {
        $input = [];
      }

      $entity_type = static::entityTypeManager()->getDefinition($element['#entity_type']);
      if (isset($element['#bundle']) && $entity_type->getBundleEntityType()) {
        $input[$entity_type->getKey('bundle')] = $element['#bundle'];
      }

      $element['#value'] = static::entityTypeManager()
        ->getStorage($element['#entity_type'])
        ->create($input);
    }
    else {
      $element['#value'] = $element['#default_value'];
    }

    return $element['#value'];
  }

  /**
   * Form element processing handler.
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @return array
   *   The processed element.
   */
  public static function processBuildForm(&$element, FormStateInterface $form_state, array &$complete_form) {
    $element['#form_object'] = static::entityTypeManager()
      ->getFormObject($element['#entity_type'], $element['#operation'])
      ->setEntity($element['#value']);

    $sub_form = $element['#form_object']->buildForm([], new FormState());
    foreach (Element::getVisibleChildren($sub_form) as $child_id) {
      if ($sub_form[$child_id]['#type'] === 'actions') {
        unset($sub_form[$child_id]);
      }
    }
    return $element + $sub_form;
  }

  /**
   * Form element processing handler. Forward 'required' states of the form element to sub-form elements.
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @return array
   *   The processed element.
   */
  public static function processConditionallyRequiredStates(array &$element, FormStateInterface $form_state, array &$complete_form) {
    foreach (ElementPlus::children($element) as $child_id) {
      if (!isset($element[$child_id]['#type'])) {
        continue;
      }

      $child = $element[$child_id];
      $states = isset($element['#states']) ? $element['#states'] : [];
      $child_states = isset($child['#states']) ? $child['#states'] : [];

      ElementState::mergeStates($child_states, $states);
      if (ElementPlus::isConditionallyRequired($element)) {
        ElementState::addState($child_states, 'required', $states['required']);

        $child['#states'] = $child_states;
        $child = static::processConditionallyRequiredStates(
          $child, $form_state, $complete_form);

        if (ElementPlus::isRequiredElement($child)) {
          unset($child['#required']);
        }
        else {
          ElementState::removeState($child['#states'], 'required');
        }
      }

      $element[$child_id] = $child;
    }

    return $element;
  }

  /**
   * Form element processing handler. Assign "parents" to sub-form children elements.
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @return array
   *   The processed element.
   */
  public static function processParents(&$element, FormStateInterface $form_state, array &$complete_form) {
    $sub_elements = array_intersect_key($element, array_flip(Element::getVisibleChildren($element)));
    foreach ($sub_elements as $child_id => $child) {
      if (!array_key_exists('#type', $child)) {
        continue;
      }
      if (empty(Element::children($child)) || (isset($child['#tree']) && $child['#tree'])) {
        $element[$child_id]['#parents'] = array_merge($element['#parents'], [$child_id]);
      }
      else {
        // TODO: Children of elements which set #tree = TRUE must add themselves as parents.
        $element[$child_id]['#parents'] = $element['#parents'];
      }
      // TODO: Form #states break when element names change due to re-assigning #parents.
      $element[$child_id] = static::processParents($element[$child_id], $form_state, $complete_form);
    }

    return $element;
  }

  /**
   * Form element validation handler.
   *
   * @param array $element
   *   The element validate.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param array $complete_form
   *   The complete form.
   */
  public static function validateSubForm(&$element, FormStateInterface $form_state, &$complete_form) {
    $sub_form_state = clone $form_state;
    $sub_form_state->setValues(NestedArray::getValue($form_state->getValues(), $element['#parents']));
    if ($entity = $element['#form_object']->validateForm($element, $sub_form_state)) {
      $form_state->setValueForElement($element, $entity);
    }
  }

}
