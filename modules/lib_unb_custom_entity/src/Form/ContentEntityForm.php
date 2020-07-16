<?php

namespace Drupal\lib_unb_custom_entity\Form;

use Drupal\Core\Entity\ContentEntityForm as DefaultContentEntityForm;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Enhances Drupal's default ContentEntityForm.
 *
 * @package Drupal\lib_unb_custom_entity\Form
 */
class ContentEntityForm extends DefaultContentEntityForm {

  /**
   * Retrieve the entity type manager service.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   An entity type manager instance.
   */
  protected function getEntityTypeManager() {
    return $this->entityTypeManager;
  }

  /**
   * Retrieve the module providing this form.
   *
   * @return string
   *   A string.
   */
  protected function getProvider() {
    return $this->getEntity()->getEntityType()->getProvider();
  }

  /**
   * {@inheritDoc}
   */
  protected function init(FormStateInterface $form_state) {
    parent::init($form_state);
    $this->prepareFormState($form_state);
  }

  /**
   * Pre-populate the form state before building the form.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  protected function prepareFormState(FormStateInterface $form_state) {
    $default_values = $this->getEntity()->isNew()
      ? $this->getDefaultValuesForNewEntity()
      : $this->getDefaultValuesForExistingEntity();

    foreach ($default_values as $field_id => $default_value) {
      $form_state->setValue($field_id, $default_value);
    }
  }

  /**
   * Retrieve values based on each field's default value.
   *
   * @return array
   *   An array of the form FIELD_ID => VALUE.
   */
  protected function getDefaultValuesForNewEntity() {
    /** @var \Drupal\Core\Entity\ContentEntityInterface $content_entity */
    $content_entity = $this->getEntity();

    $default_values = [];
    foreach ($content_entity->getFields() as $field_id => $field) {
      if ($default_value = $field->getFieldDefinition()->getDefaultValue($content_entity)) {
        $default_values[$field_id] = $default_value;
      }
      elseif ($field->getFieldDefinition()->getFieldStorageDefinition()->getCardinality() !== 1) {
        $default_values[$field_id] = [];
      }
      else {
        $default_values[$field_id] = NULL;
      }
    }
    return $default_values;
  }

  /**
   * Retrieve values based on an existing entity.
   *
   * @return array
   *   An array of the form FIELD_ID => VALUE(S).
   */
  protected function getDefaultValuesForExistingEntity() {
    /** @var \Drupal\Core\Entity\ContentEntityInterface $content_entity */
    $content_entity = $this->getEntity();

    $default_values = [];
    foreach ($content_entity->getFields() as $field_id => $field) {
      if ($field->getFieldDefinition()->getFieldStorageDefinition()->getCardinality() !== 1) {
        $default_values[$field_id] = $this->getMultiItemFieldValues($field);
      }
      else {
        $default_values[$field_id] = $this->getSingleItemFieldValue($field);
      }
    }
    return $default_values;
  }

  /**
   * Retrieve the value of a single-item field.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $field
   *   The field to parse.
   *
   * @return mixed|null
   *   A value or, if the field's schema consists
   *   of more than one column, an array of values.
   */
  private function getSingleItemFieldValue(FieldItemListInterface $field) {
    $columns = array_keys($field->getFieldDefinition()->getFieldStorageDefinition()->getColumns());

    if (!empty($field_value = $field->getValue())) {
      if (count($columns) > 1) {
        // single-value, multi-column
        foreach (array_keys($columns) as $column_id) {
          $value[$column_id] = $field_value[0][$column_id];
        }
      }
      else {
        // single-value, single column
        $column_id = $columns[0];
        $value = $field_value[0][$column_id];
      }
    }

    return isset($value) ? $value : NULL;
  }

  /**
   * Retrieve the values of a multi-item field.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $field
   *   The field to parse.
   *
   * @return array
   *   An array of arrays. If the field's schema
   *   consists of more than one column, the value
   *   will also be an array.
   *
   */
  private function getMultiItemFieldValues(FieldItemListInterface $field) {
    $columns = array_keys($field->getFieldDefinition()->getFieldStorageDefinition()->getColumns());

    $values = [];
    foreach ($field->getValue() as $index => $value) {
      if (count($columns) > 1) {
        foreach ($columns as $column_id) {
          $values[$index][$column_id] = $value[$index][$column_id];
        }
      }
      else {
        $column_id = $columns[0];
        $values[] = $value[$column_id];
      }
    }

    return $values;
  }

}
