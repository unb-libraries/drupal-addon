<?php

namespace Drupal\subforms\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\FormElement;
use Drupal\Core\Render\Element\Container;
use Drupal\Core\Render\Element\Fieldset;

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
  protected $entityTypeManager;

  /**
   * The entity form builder service.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilderInterface
   */
  protected $entityFormBuilder;

  /**
   * Retrieve an entity type manager service instance.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   An entity type manager.
   */
  protected function entityTypeManager() {
    if (!isset($this->entityTypeManager)) {
      $this->entityTypeManager = \Drupal::entityTypeManager();
    }
    return $this->entityTypeManager;
  }

  /**
   * Retrieve a form builder service instance.
   *
   * @return \Drupal\Core\Entity\EntityFormBuilderInterface
   *   A form builder.
   */
  protected function entityFormBuilder() {
    if (!isset($this->entityFormBuilder)) {
      $this->entityFormBuilder = \Drupal::service('entity.form_builder');
    }
    return $this->entityFormBuilder;
  }

  /**
   * {@inheritDoc}
   */
  public function getInfo() {
    return [
      '#entity_type' => '',
      '#operation' => 'default',
      '#pre_render' => [
        [get_class($this), 'preRenderGroup'],
      ],
      '#process' => [
        [$this, 'processContainerOrFieldset'],
        [$this, 'processBuildForm'],
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
  public function processContainerOrFieldset(&$element, FormStateInterface $form_state, array &$complete_form) {
    if (array_key_exists('#title', $element)) {
      $element['#process'][] = [get_class($this), 'processAjaxForm'];
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
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function processBuildForm(&$element, FormStateInterface $form_state, array &$complete_form) {
    $entity = $element['#default_value'] ?: $this
      ->entityTypeManager()
      ->getStorage($element['#entity_type'])
      ->create();

    $sub_form = \Drupal::service('entity.form_builder')
      ->getForm($entity);

    foreach (Element::getVisibleChildren($sub_form) as $child) {
      if ($child !== 'actions') {
        $element[$child] = $sub_form[$child];
      }
    }

    return $element;
  }

}
