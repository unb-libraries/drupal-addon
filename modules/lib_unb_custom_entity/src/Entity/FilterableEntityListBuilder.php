<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\lib_unb_custom_entity\Form\EntityFilterForm;

/**
 * Build a listing of entities that can be filtered by HTTP GET parameters.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
class FilterableEntityListBuilder extends EntityListBuilder {

  const PARAM_OPERANDS_MAP = [
    'gt' => '>',
    'gte' => '>=',
    'lt' => '<',
    'lte' => '<=',
    'eq' => '=',
    'not' => '<>',
    'starts_with' => 'STARTS_WITH',
    'ends_with' => 'ENDS_WITH',
    'in' => 'IN',
    'not_in' => 'NOT IN',
  ];

  /**
   * All values considered "any", i.e. "do no filter".
   *
   * @var array
   */
  protected $anyValues = ['', 'any', 'all'];

  /**
   * All values considered "none", i.e. "explicitly filter by non-existing values".
   *
   * @var array
   */
  protected $nullValues = ['null', 'none'];

  /**
   * Fields on which can be filtered.
   *
   * @var array
   */
  protected $fields;

  /**
   * The form object used to control the filtering of this list.
   *
   * @var \Drupal\lib_unb_custom_entity\Form\EntityFilterForm
   */
  protected $form;

  /**
   * Retrieve the form object used to control the filtering of this list.
   *
   * @return \Drupal\lib_unb_custom_entity\Form\EntityFilterForm
   */
  protected function getForm() {
    if (!isset($this->form) && $form_class = $this->getFormClass()) {
      $this->form = new $form_class($this->getEntityType());
    }
    return $this->form;
  }

  /**
   * Build a filter form for this list.
   *
   * @return array
   *   A render array.
   */
  public function buildForm() {
    if ($form = $this->getForm()) {
      return [
        '#type' => 'form',
      ] + \Drupal::formBuilder()->getForm($form);
    }
    return [];
  }

  /**
   * Retrieve the form class to use for filtering this list.
   *
   * @return string
   *   A class name string.
   */
  protected function getFormClass() {
    if ($form_class = $this->getEntityType()->getFormClass('filter')) {
      if (is_subclass_of($form_class, EntityFilterForm::class)) {
        return $form_class;
      }
    }
    return NULL;
  }

  /**
   * Build the query to populate this entity list.
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   The query.
   */
  protected function getEntityQuery() {
    $query = parent::getEntityQuery();
    foreach ($this->getRequestParams() as $param => $value) {
      list($field_id, $op) = $this->parseParam($param);
      if ($field_id && $op) {
        $value = $this->parseValue($value);
        $this->addCondition($query, $field_id, $value, $op);
      }
    }
    return $query;
  }

  /**
   * Add a query condition to the given query.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   *   The query.
   * @param string $field_id
   *   The field.
   * @param array|string $value
   *   The value or values.
   * @param string $op
   *   The condition operand.
   */
  protected function addCondition(QueryInterface &$query, $field_id, $value, $op) {
    if (!is_array($value)) {
      if ($this->isNullValue($value)) {
        $query->notExists($field_id);
      }
      elseif (!$this->isAnyValue($value)) {
        $query->condition($field_id, $value, $op);
      }
    }
    else {
      if (!empty($value)) {
        if ($this->containsNullValue($value)) {
          $condition_group = $query->orConditionGroup();
          $condition_group->condition($field_id, $value, $op);
          $condition_group->notExists($field_id);
          $query->condition($condition_group);
        }
        else {
          $query->condition($field_id, $value, $op);
        }
      }
    }
  }

  /**
   * Parse a string of the form PARAM::OP into separate variables.
   *
   * @param $param
   *   A string containing both a param and an operand.
   *
   * @return array|bool
   *   Array containing a field ID and an operand.
   *   FALSE if the string could not be parsed.
   */
  protected function parseParam($param) {
    if (!empty($field_id_and_op = explode('::', $param))) {
      if (!in_array($field_id = $field_id_and_op[0], $this->filterableFieldIds())) {
        return FALSE;
      }

      $op = count($field_id_and_op) > 1
        ? $this->toQueryOperand($field_id_and_op[1])
        : $this->toQueryOperand('');
      return [$field_id, $op];
    }

    return FALSE;
  }

  /**
   * Parse the value, i.e. leave as is or convert into array.
   *
   * @param string $value
   *   A string.
   *
   * @return array|string
   *   A single or an array of literal values.
   */
  protected function parseValue($value) {
    if (!$value) {
      return '';
    }
    $values = explode(';', $value);
    return count($values) > 1 ? $values : $values[0];
  }

  /**
   * Whether the given value means "any".
   *
   * @param string $value
   *   The value.
   *
   * @return bool
   *   TRUE if the given value is either empty, "any", or "all".
   *   FALSE otherwise.
   */
  protected function isAnyValue($value) {
    return in_array(strtolower($value), $this->getAnyValues());
  }

  /**
   * Retrieve all values that mean "any".
   *
   * @return array
   *   An array of strings.
   */
  protected function getAnyValues() {
    return $this->anyValues;
  }

  /**
   * Whether the given array of value contains at least one "null" value.
   *
   * @param array $values
   *   The value.
   *
   * @return bool
   *   TRUE if the given array of value contains at least
   *   one "null" value. FALSE otherwise.
   */
  protected function containsNullValue(array $values) {
    foreach ($values as $value) {
      if ($this->isNullValue($value)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Whether the given value means "none".
   *
   * @param string $value
   *   The value.
   *
   * @return bool
   *   TRUE if the given value is either "none" or "null".
   *   FALSE otherwise.
   */
  protected function isNullValue($value) {
    return in_array(strtolower($value), $this->getNullValues());
  }

  /**
   * Retrieve all values that mean "none".
   *
   * @return array
   *   An array of strings.
   */
  protected function getNullValues() {
    return $this->nullValues;
  }

  /**
   * Maps an HTTP GET operand to an operand that can be used in an entity query condition.
   *
   * @param $op
   *   The HTTP GET operand, e.g. 'gte'.
   *
   * @return string
   *   A QueryInterface operand, e.g. '>='.
   */
  protected function toQueryOperand($op) {
    $query_operands = $this->queryOperands();
    if ($op && array_key_exists($op, $query_operands)) {
      return $query_operands[$op];
    }
    return '=';
  }

  /**
   * Retrieve a map of HTTP GET operands and QueryInterface operands.
   *
   * @return array
   *   Operand map containing entries of the form
   *   HTTP_QUERY_OPERAND => ENTITY_QUERY_OPERAND.
   */
  protected function queryOperands() {
    return self::PARAM_OPERANDS_MAP;
  }

  /**
   * Retrieve all request parameters, if any.
   *
   * @return array
   *   Array of request parameters of the form PARAM => VALUE.
   *   If no parameters have been passed, an empty array is returned.
   */
  protected function getRequestParams() {
    return \Drupal::request()->query->all();
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    return [
      'form' => $this->buildForm(),
    ] + parent::render();
  }

  /**
   * {@inheritDoc}
   */
  protected function cacheContexts() {
    $contexts = parent::cacheContexts();
    $context_base = 'url.query_args';
    foreach ($this->filterableFieldIds() as $field_id) {
      $contexts[] = sprintf('%s:%s', $context_base, $field_id);
      foreach (array_keys($this->queryOperands()) as $op)
        $contexts[] = sprintf('%s:%s::%s', $context_base, $field_id, $op);
    }
    return $contexts;
  }

  /**
   * Retrieve all entity fields by which this list shall be filterable.
   *
   * @return array
   *   Array of entity field IDs.
   */
  protected function filterableFieldIds() {
    if (!isset($this->fields)) {
      /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $field_manager */
      $field_manager = \Drupal::service('entity_field.manager');
      $this->fields = [];
      foreach ($field_manager->getFieldStorageDefinitions($this->getStorage()->getEntityTypeId()) as $field_id => $field_definition) {
        $columns = $field_definition->getSchema()['columns'];
        if (count($columns) > 1) {
          foreach (array_keys($columns) as $column_id) {
            $this->fields[] = sprintf('%s__%s', $field_id, $column_id);
          }
        }
        else {
          $this->fields[] = $field_id;
        }
      }
    }
    return $this->fields;
  }

}
