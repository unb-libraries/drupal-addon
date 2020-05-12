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
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function preRenderSelect($element) {
    if ($entity_type_id = $element['#entity_type']) {
      $entity_type = static::entityTypeManager()->getDefinition($entity_type_id);
      if (($bundle = $element['#bundle']) && $entity_type->hasKey('bundle')) {
        $entities = static::loadBundleEntities($entity_type, $bundle);
      }
      else {
        $entities = self::loadEntities($entity_type);
      }
      $element['#options'] = FormHelper::entityLabels($entities);
    }
    return parent::preRenderSelect($element);
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

}
