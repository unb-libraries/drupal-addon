<?php

namespace Drupal\subforms\Element;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
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
   * The entity form builder service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected static $subFormBuilder;

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
   * @return \Drupal\Core\Form\FormBuilderInterface
   *   A form builder.
   */
  protected static function subFormBuilder() {
    if (!isset(static::$subFormBuilder)) {
      static::$subFormBuilder = \Drupal::service('subform_builder');
    }
    return static::$subFormBuilder;
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
    $element['#form_state']->addBuildInfo('parents', $element['#parents']);
    if (array_key_exists('#states', $element)) {
      $element['#form_state']->addBuildInfo('states', $element['#states']);
    }

    if ($input) {
      $element['#form_state']->setValues($input);
      static::subFormBuilder()
        ->submitForm($element['#form_object'], $element['#form_state']);
    }

    $element['#form'] = static::subFormBuilder()
      ->buildForm($element['#form_object'], $element['#form_state']);

    $element['#value'] = $element['#form_object']
      ->getEntity();

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
    $children = array_intersect_key($element['#form'], array_flip(ElementPlus::children($element['#form'])));
    foreach ($children as $child_id => $child) {
      if (isset($child['#type']) && $child['#type'] !== 'actions') {
        $element[$child_id] = $child;
      }
    }

    $element['#attributes']['class'][] = 'form-subform';
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
    $form_state->setValueForElement($element, $element['#value']);
  }

}
