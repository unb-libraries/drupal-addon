<?php

namespace Drupal\subforms\Element;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\CompositeFormElementTrait;
use Drupal\Core\Render\Element\Container;
use Drupal\Core\Render\Element\FormElement;

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
   * The element builder service.
   *
   * @var \Drupal\subforms\Element\ElementBuilderInterface
   */
  protected static $elementBuilder;

  /**
   * The element state builder.
   *
   * @var \Drupal\subforms\Element\ElementStateBuilderInterface
   */
  protected static $_elementStateBuilder;

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
   * Retrieve the element builder service.
   *
   * @return \Drupal\subforms\Element\ElementBuilderInterface
   *   An element builder instance.
   */
  protected static function elementBuilder() {
    if (!isset(static::$elementBuilder)) {
      static::$elementBuilder = \Drupal::service('element_builder');
    }
    return static::$elementBuilder;
  }

  /**
   * Retrieve the element state builder.
   *
   * @return \Drupal\subforms\Element\ElementStateBuilderInterface
   *   An element state builder instance.
   */
  protected static function elementStateBuilder() {
    if (!isset(static::$_elementStateBuilder)) {
      static::$_elementStateBuilder = \Drupal::service('element_builder.state');
    }
    return static::$_elementStateBuilder;
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
        [static::class, 'processStates'],
        [static::class, 'processGroup'],
      ],
      '#element_validate' => [
        [static::class, 'validateSubForm'],
      ],
    ];
  }

  /**
  * {@inheritdoc}
  */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    $element['#tree'] = TRUE;

    $element['#form_object'] = static::entityTypeManager()
      ->getFormObject($element['#entity_type'], $element['#operation'])
      ->setEntity(static::getEntity($element));

    $element['#form_state'] = new FormState();
    $element['#parent_form_state'] = $form_state;
    $element['#form_state']->setFormObject($element['#form_object']);

    $element['#form'] = $element['#form_object']
      ->buildForm(['#type' => 'container'], $element['#form_state']);
    $element['#form']['#parents'] = $element['#parents'];
    static::prepareSubForm($element['#form'], $element['#form_state']);

    if ($input) {
      $element['#form_state']->setUserInput($input);
      $element['#value'] = static::getSubFormValue($element['#form'], $element['#form_state']);
    }
    else {
      $element['#value'] = $element['#default_value'];
    }

    return $element['#value'];
  }

  /**
   * Build an entity object based on the given element.
   *
   * @param array $element
   *   The element.
   * @return \Drupal\Core\Entity\EntityInterface
   *   An entity object.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected static function getEntity(array $element) {
    if (static::isEntity($element['#default_value'], $element)) {
      $entity = $element['#default_value'];
    }
    else {
      $values = [];
      $entity_type = static::entityTypeManager()->getDefinition($element['#entity_type']);
      if (isset($element['#bundle']) && $entity_type->getBundleEntityType()) {
        $values[$entity_type->getKey('bundle')] = $element['#bundle'];
      }

      $entity = static::entityTypeManager()
        ->getStorage($element['#entity_type'])
        ->create($values);
    }

    return $entity;
  }

  /**
   * Whether the given value is a valid entity according to the provided element definition.
   *
   * @param mixed $value
   *   The value to check.
   * @param array $element
   *   The element.
   *
   * @return bool
   *   TRUE if the given value is a valid entity object of the
   *   type as defined by the given element. FALSE otherwise.
   */
  protected static function isEntity($value, array $element) {
    return isset($value)
      && $value instanceof EntityInterface
      && $value->getEntityTypeId() === $element['#entity_type'];
  }

  /**
   * Prepare the form for further processing.
   *
   * @param array $form
   *   The current form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  protected static function prepareSubForm(array &$form, FormStateInterface &$form_state) {
    foreach (Element::children($form) as $field_id) {
      static::elementBuilder()->prepareElement($field_id, $form[$field_id], $form, $form_state);
    }
  }

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
  protected static function getSubFormValue(array &$form, FormStateInterface $form_state) {
    $form_state->setValues([]);
    foreach (Element::children($form) as $field_id) {
      static::elementBuilder()->getElementValue($form[$field_id], $form, $form_state);
    }

    /** @var \Drupal\Core\Entity\EntityFormInterface $form_obj */
    $form_obj = $form_state->getFormObject();

    return $form_obj->buildEntity($form, $form_state);
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
    foreach (Element::children($element['#form']) as $child_id) {
      if ($element['#form'][$child_id]['#type'] !== 'actions') {
        $element[$child_id] = $element['#form'][$child_id];
      }
    }

    return $element;
  }

  /**
   * Process the states of the subform, i.e. pass any state definitions down to the children.
   *
   * @param array $element
   *   An element render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form render array.
   */
  public static function processStates(&$element, FormStateInterface $form_state, array &$complete_form) {
    foreach (Element::children($element) as $child_id) {
      $has_children = !empty(Element::children($element[$child_id]));
      if (isset($element['#states']) && isset($element[$child_id]['#type'])) {
        static::elementStateBuilder()->mergeElementStates($element[$child_id], $element);
        if ((static::elementStateBuilder()->isConditionallyRequired($element) || static::elementStateBuilder()->isConditionallyOptional($element[$child_id])) && (static::elementBuilder()->isRequired($element[$child_id]) || $has_children)) {
          // if the element is conditionally required, convert required children or children with children
          // into conditionally required children.
          if (isset($element[$child_id]['#required'])) {
            unset($element[$child_id]['#required']);
          }
        }
      }
      static::processStates($element[$child_id], $form_state, $complete_form);
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
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $element['#form_object']->validateForm($element, $element['#form_state']);

    foreach ($element['#form_state']->getErrors() as $field_name => $error) {
      $form_state->setErrorByName($field_name, $error);
    }
    $element_id = $element['#parents'][count($element['#parents']) - 1];
    $element['#parent_form_state']->setValue([$element_id], $entity);
    $element['#form_object']->setEntity($entity);
  }

}
