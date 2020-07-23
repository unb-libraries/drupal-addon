<?php

namespace Drupal\subforms\Element;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
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
        [static::class, 'preRenderGroup'],
      ],
      '#process' => [
        [static::class, 'processContainerOrFieldset'],
        [static::class, 'processBuildForm'],
        [static::class, 'processParents'],
        [static::class, 'processGroup'],
      ],
      '#element_validate' => [
        [static::class, 'validateForm'],
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
    if (isset($element['#default_value'])) {
      $entity = $element['#default_value'];
    }
    else {
      $entity_type = static::entityTypeManager()
        ->getDefinition($element['#entity_type']);
      if (isset($element['#bundle']) && $entity_type->getBundleEntityType()) {
        $values[$entity_type->getKey('bundle')] = $element['#bundle'];
      }
      else {
        $values = [];
      }

      $entity = static::entityTypeManager()
        ->getStorage($element['#entity_type'])
        ->create($values);
    }

    return $entity;
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
  public static function validateForm(&$element, FormStateInterface $form_state, &$complete_form) {
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $element['#value'];

    // TODO: Sanitize the user input or find out how to populate the form state with validated data.
    $values = array_intersect_key($form_state->getUserInput(), NestedArray::getValue($form_state->getValues(), $element['#parents']));
    foreach ($values as $key => $value) {
      $entity->set($key, $value);
    }
    $form_state->setValueForElement($element, $entity);
  }

}
