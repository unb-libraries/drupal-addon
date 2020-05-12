<?php

namespace Drupal\lib_unb_custom_entity\Element;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Annotation\FormElement;
use Drupal\Core\Render\Element\Select;
use Drupal\lib_unb_custom_entity\Form\FormHelper;

/**
 * Renders a form "select" element containing entities of a given type as its options.
 *
 * Properties:
 *   - #entity_type: (string) ID of the entity type which the select options will be populated with.
 *   - #bundle: (string) limit the select options to the given bundle value.
 *
 * Usage example:
 * @code
 * $form['entity'] = [
 *   '#type' => 'entity_select',
 *   '#title' => $this->t('Entity'),
 *   '#entity_type' => 'node',
 *   '#bundle' => 'post'
 * ];
 * @endcode
 *
 * @FormElement("entity_select")
 */
class EntitySelect extends Select {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected static $entityTypeManager;

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
   * {@inheritDoc}
   */
  public function getInfo() {
    return parent::getInfo() + [
      '#entity_type' => 'node',
      '#bundle' => '',
    ];
  }

  /**
   * Overrides @see \Drupal\Core\Render\Element\Select::preRenderSelect().
   *
   * Populates the element options with entities of the configured type.
   *
   * {@inheritDoc}
   */
  public static function preRenderSelect($element) {
    $element['#options'] = static::buildOptions($element);
    return parent::preRenderSelect($element);
  }

  /**
   * Overrides @see \Drupal\Core\Render\Element\Select::processSelect().
   *
   * Populates the element options with entities of the configured type.
   *
   * {@inheritDoc}
   */
  public static function processSelect(&$element, FormStateInterface $form_state, &$complete_form) {
    $element['#options'] = static::buildOptions($element);
    return parent::processSelect($element, $form_state, $complete_form);
  }

  /**
   * Create the options for the select element.
   *
   * @param $element
   *   The element.
   *
   * @return array
   *   An array of the form VALUE => LABEL.
   */
  protected static function buildOptions($element) {
    try {
      $entity_type = static::entityTypeManager()->getDefinition($element['#entity_type']);
      if (($bundle = $element['#bundle']) && $entity_type->hasKey('bundle')) {
        $entities = static::loadBundleEntities($entity_type, $bundle);
      }
      else {
        $entities = self::loadEntities($entity_type);
      }
      return FormHelper::entityLabels($entities);
    }
    catch (\Exception $e) {
      // TODO: This should not go silent.
      return [];
    }
  }

  /**
   * Load entities of the given type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entities.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected static function loadEntities(EntityTypeInterface $entity_type) {
    return static::entityTypeManager()
      ->getStorage($entity_type->id())
      ->loadMultiple();
  }

  /**
   * Load entities of the given type and bundle.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entities.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected static function loadBundleEntities(EntityTypeInterface $entity_type, $bundle) {
    $bundle_field = $entity_type->getKey('bundle');
    return static::entityTypeManager()
      ->getStorage($entity_type->id())
      ->loadByProperties([
        $bundle_field => $bundle,
      ]);
  }

  /**
   * {@inheritDoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    if ($input = parent::valueCallback($element, $input, $form_state)) {
      $entity = static::entityTypeManager()
        ->getStorage($element['#entity_type'])
        ->load($input);
      $form_state->setValueForElement($element, $entity);
      return $input;
    }
    return parent::valueCallback($element, $input, $form_state);
  }

}
