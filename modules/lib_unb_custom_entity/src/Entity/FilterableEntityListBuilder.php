<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\lib_unb_custom_entity\Form\EntityFilterForm;

/**
 * Build a listing of entities that can be filtered by HTTP GET parameters.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
class FilterableEntityListBuilder extends EntityListBuilder {

  protected const PARAM_OPERANDS_MAP = [
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
   * Fields on which can be filtered.
   *
   * @var array
   */
  protected $fields;

  /**
   * The form object used to control the filtering of this list.
   *
   * @var \Drupal\Core\Form\FormInterface
   */
  protected $form;

  /**
   * Retrieve the form object used to control the filtering of this list.
   *
   * @return \Drupal\Core\Form\FormInterface
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
    return \Drupal::formBuilder()->getForm($this->getForm());
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
      if ((list($field_id, $op) = $this->parseParam($param)) && ($value = $this->parseValue($value))) {
        // TODO: Enable parsing multiple (i.e. array) values.
        $query->condition($field_id, $value, $op);
      }
    }
    return $query;
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
   * @param $value
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
