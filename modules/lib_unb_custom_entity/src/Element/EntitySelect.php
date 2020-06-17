<?php

namespace Drupal\lib_unb_custom_entity\Element;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Annotation\FormElement;
use Drupal\Core\Render\Element\Select;

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
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected static $entityFieldManager;

  /**
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  protected static $tagStorage;

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
   * Retrieve an entity field manager service instance.
   *
   * @return \Drupal\Core\Entity\EntityFieldManagerInterface
   *   An entity field manager.
   */
  protected static function entityFieldManager() {
    if (!isset(static::$entityFieldManager)) {
      static::$entityFieldManager = \Drupal::service('entity_field.manager');
    }
    return static::$entityFieldManager;
  }

  /**
   * Retrieve a storage handler for taxonomy term entities.
   *
   * @return \Drupal\taxonomy\TermStorageInterface
   *   A storage handler for taxonomy term entities.
   */
  protected static function tagStorage() {
    if (!isset(static::$tagStorage)) {
      /** @noinspection PhpUnhandledExceptionInspection */
      static::$tagStorage = static::entityTypeManager()->getStorage('taxonomy_term');
    }
    return static::$tagStorage;
  }

  /**
   * {@inheritDoc}
   */
  public function getInfo() {
    return parent::getInfo() + [
      '#entity_type' => 'node',
      '#bundle' => '',
      '#tags' => [],
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
      return array_map(function (EntityInterface $entity) {
        return $entity->label();
      }, static::loadEntities($element));
    }
    catch (\Exception $e) {
      // TODO: This should not go silent.
      return [];
    }
  }

  /**
   * Load entities of the type stated in the given element.
   *
   * @param array $element
   *   The entity type.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entities.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected static function loadEntities(array $element) {
    $entity_type = static::getEntityType($element);
    return static::entityTypeManager()
      ->getStorage($entity_type->id())
      ->loadMultiple(static::entityQuery($element)->execute());
  }

  /**
   * Retrieve an entity query object.
   *
   * @param array $element
   *   The element.
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   An entity query object.
   */
  protected static function entityQuery(array $element) {
    $entity_type = static::getEntityType($element);
    $query = \Drupal::entityQuery($entity_type->id());
    if (($bundle = $element['#bundle']) && $entity_type->hasKey('bundle')) {
      $query->condition($entity_type->getKey('bundle'), $bundle);
    }
    else {
      $bundle = $entity_type->id();
    }

    if (!empty($element['#tags']) && $tag_ids = array_keys(static::loadTags($element))) {
      $entity_type = static::getEntityType($element);
      $field_definitions = static::entityFieldManager()->getFieldDefinitions($entity_type->id(), $bundle);
      $tag_fields = array_filter($field_definitions, function (FieldDefinitionInterface $field_definition) {
        $field_settings = $field_definition->getFieldStorageDefinition()->getSettings();
        return array_key_exists('target_type', $field_settings)
          && $field_settings['target_type'] == 'taxonomy_term';
      });

      $field_condition_group = $query->orConditionGroup();
      foreach ($tag_fields as $tag_field) {
        if ($tag_field->getFieldStorageDefinition()->getCardinality() === BaseFieldDefinition::CARDINALITY_UNLIMITED) {
          $tag_condition_group = $query->orConditionGroup();
          foreach ($tag_ids as $tag_id) {
            $tag_condition_group->condition($tag_field->getName(), $tag_id, 'CONTAINS');
          }
          $field_condition_group->condition($tag_condition_group);
        }
        else {
          $field_condition_group->condition($tag_field->getName(), $tag_ids, 'IN');
        }
      }
      $query->condition($field_condition_group);
    }

    return $query;
  }

  /**
   * Load the taxonomy term entities as defined by the given element.
   *
   * @param array $element
   *   The element.
   *
   * @return \Drupal\taxonomy\TermInterface[]
   *   An array of taxonomy term entities.
   */
  protected static function loadTags(array $element) {
    $tag_storage = static::tagStorage();
    $tag_query = $tag_storage->getQuery();

    foreach ($element['#tags'] as $vid => $tag_names) {
      /** @noinspection PhpUnhandledExceptionInspection */
      $vid_field = static::entityTypeManager()->getDefinition('taxonomy_term')->getKey('bundle');
      if (!empty($tag_names)) {
        $tag_query->condition($vid_field, $vid, '=');
        $tag_query->condition('name', $tag_names, 'IN');
      }
      else {
        $tag_query->condition($vid_field, $vid, '<>');
      }
    }

    /** @var \Drupal\taxonomy\TermInterface[] $tags */
    if (!empty($tag_ids = $tag_query->execute())) {
      $tags = $tag_storage->loadMultiple($tag_ids);
    }
    else {
      $tags = $tag_storage->loadMultiple();
    }
    return $tags;
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

  /**
   * Derive the entity type from the given element.
   *
   * @param array $element
   *   The element.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface|null
   *   An entity type object.
   */
  protected static function getEntityType(array $element) {
    if (!$entity_type_id = $element['#entity_type']) {
      $entity_type_id = 'node';
    }
    /** @noinspection PhpUnhandledExceptionInspection */
    return static::entityTypeManager()
      ->getDefinition($entity_type_id);
  }

}
