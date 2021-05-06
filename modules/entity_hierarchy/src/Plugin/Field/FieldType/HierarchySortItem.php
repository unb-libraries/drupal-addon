<?php

namespace Drupal\entity_hierarchy\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\StringItem;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\entity_hierarchy\Entity\SortableHierarchicalInterface;

/**
 * Defines the 'hierarchy_sort' entity field type.
 *
 * @FieldType(
 *   id = "hierarchy_sort",
 *   label = @Translation("Hierarchy sort"),
 *   description = @Translation("A string value indicating a hierarchy sort order."),
 *   no_ui = TRUE,
 *   cardinality = 1,
 *   default_widget = "string_textfield",
 *   default_formatter = "string"
 * )
 */
class HierarchySortItem extends StringItem {

  protected const FIELD = 'field';
  protected const DELIMITER = 'delimiter';
  protected const FILL = 'fill';
  protected const CHUNK_SIZE = 'chunk_size';

  /**
   * {@inheritDoc}
   */
  public static function defaultStorageSettings() {
    return [
      self::CHUNK_SIZE => 5,
      self::FIELD => NULL,
      self::DELIMITER => '#',
      self::FILL => '0',
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritDoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);
    $schema['columns']['value_global'] = [
      'type' => $field_definition->getSetting('is_ascii') === TRUE ? 'varchar_ascii' : 'varchar',
      'length' => (int) $field_definition->getSetting('max_length'),
      'binary' => $field_definition->getSetting('case_sensitive'),
    ];
    return $schema;
  }

  /**
   * {@inheritDoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);
    $properties['value']
      ->setLabel(t('Value'))
      ->setDescription(t("Determines this item's sort position within its level of the hierarchy ."));

    $properties['value_global'] = DataDefinition::create('string')
      ->setLabel(t('Global value'))
      ->setDescription(t("Determines this item's sort position within the entire hierarchy."))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritDoc}
   */
  public function preSave() {
    parent::preSave();
    $this->applyDefaultValue();
  }

  /**
   * {@inheritDoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $field_manager */
    $field_manager = \Drupal::service('entity_field.manager');
    $entity_type_id = $field_definition->getTargetEntityTypeId();
    $field_definitions = $field_manager->getBaseFieldDefinitions($entity_type_id);

    $samples = [];
    foreach ($field_definition->getSetting(self::FIELDS) as $field_name) {
      $field_item_class = $field_definitions[$field_name]->getClass();
      $sample = call_user_func([$field_item_class, 'generateSampleValue'], $field_definition[$field_name]);
      str_pad($sample, $field_definition->getSetting(self::CHUNK_SIZE) + 1, $field_definition->getSetting(self::FILL));
    }

    $delimiter = $field_definition->getSetting(self::DELIMITER);
    $values['value'] = implode($delimiter, $samples);
    return $values;
  }

  /**
   * {@inheritDoc}
   */
  public function applyDefaultValue($notify = TRUE) {
    $value = $this->getBaseFieldValue();
    $global_key = $value;

    $entity = $this->getEntity();
    if ($entity instanceof SortableHierarchicalInterface) {
      $field_name = SortableHierarchicalInterface::FIELD_SORT_KEY;
      if ($superior = $entity->getSuperior()) {
        if (!$superior_key = $superior->get($field_name)->value_global) {
          $superior->get($field_name)
            ->applyDefaultValue();
          $superior_key = $superior->get($field_name)
            ->value_global;
        }
        $global_key = $superior_key . $this->getDelimiter() . $global_key;
      }
    }

    $this->setValue([
      'value' => $value,
      'value_global' => $global_key,
    ], $notify);

    return $this;
  }

  /**
   * The delimiter that separates parts of the hierarchical sort key.
   *
   * @return string
   *   A string.
   */
  protected function getDelimiter() {
    return $this->getFieldDefinition()
      ->getSetting(self::DELIMITER);
  }

  /**
   * Retrieve the minimum length the local sort key must have.
   *
   * @return int
   *   An integer >= 0.
   */
  protected function getChunkSize() {
    return $this->getFieldDefinition()
      ->getSetting(self::CHUNK_SIZE);
  }

  /**
   * Retrieve the character(s) used as fill a chunk to its minimum size.
   *
   * @return string
   *   A string.
   */
  protected function getFill() {
    return $this->getFieldDefinition()
      ->getSetting(self::FILL);
  }

  /**
   * Retrieve the value of the base field.
   *
   * @return string
   *   A string. If the base field is a multi-value field, all field values
   *   will be concatenated.
   */
  protected function getBaseFieldValue() {
    $value = $this->getEntity()->get($this->getBaseFieldName())
      ->getValue()[0];
    $chunk_size = $this->getChunkSize();
    $chunk = substr($value[array_keys($value)[0]], 0, $chunk_size);
    $padded_value = str_pad($chunk, $chunk_size, $this->getFill(), STR_PAD_RIGHT);
    return strtoupper($padded_value);
  }

  /**
   * Retrieve the name of the field that the sort key should be based on.
   *
   * @return string
   *   A string.
   */
  protected function getBaseFieldName() {
    $field_name = $this->getFieldDefinition()
      ->getSetting(self::FIELD);

    $label_field_name = $this->getEntity()
      ->getEntityType()
      ->getKey('label');

    if ($this->getEntity()->getEntityType()->getBundleEntityType()) {
      $bundle_field_name = $this->getEntity()
        ->getEntityType()
        ->getKey('bundle');
    }
    else {
      $bundle_field_name = '';
    }

    if (!$field_name) {
      $field_name = $label_field_name;
    }

    if ($bundle_field_name && is_array($field_name)) {
      $bundle = $this->getEntity()
        ->get($bundle_field_name)
        ->target_id;
      if (array_key_exists($bundle, $field_name)) {
        $field_name = $field_name[$bundle];
      }
      elseif (array_key_exists('default', $field_name)) {
        $field_name = $field_name['default'];
      }
      else {
        $field_name = $label_field_name;
      }
    }

    return $field_name;
  }

}
