<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityListBuilder;

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
  ];

  /**
   * Build a filter form for this list.
   *
   * @return array
   *   A render array.
   */
  public function buildForm() {
    return \Drupal::formBuilder()
      ->getForm($this->getFormClass());
  }

  /**
   * Retrieve the form class to use for filtering this list.
   *
   * @return string
   *   A class name string.
   */
  protected function getFormClass() {
    $form_class = $this->getEntityType()->getFormClass('filter');
    return $form_class;
  }

  /**
   * Retrieve the entity type ID.
   *
   * @return string
   *   A string.
   */
  public function getEntityTypeId() {
    return $this->entityTypeId;
  }

  /**
   * Retrieve the entity type.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   An entity type definition.
   */
  public function getEntityType() {
    return $this->entityType;
  }

  /**
   * {@inheritDoc}
   */
  protected function getEntityIds() {
    return $this->getEntityQuery()->execute();
  }

  /**
   * Build the query to populate this entity list.
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   The query.
   */
  protected function getEntityQuery() {
    $query = $this->getStorage()->getQuery();
    foreach ($this->getRequestParams() as $param => $value) {
      if (list($field_id, $op) = $this->parseParam($param)) {
        // TODO: Enable parsing multiple (i.e. array) values.
        $query->condition($field_id, $value, $op);
      }
    }
    return $query;
  }

  /**
   * Parse a string of the form PARAM__OP into separate variables.
   *
   * @param $param
   *   A string containing both a param and an operand.
   *
   * @return array|bool
   *   Array containing a field ID and an operand.
   *   FALSE if the string could not be parsed.
   */
  protected function parseParam($param) {
    if (!empty($field_id_and_op = explode('__', $param))) {
      $field_id = $field_id_and_op[0];
      $op = count($field_id_and_op) > 1
        ? $this->toQueryOperand($field_id_and_op[1])
        : $this->toQueryOperand('');
      return [$field_id, $op];
    }
    return FALSE;
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
    // TODO: Enable operands for multiple value fields, e.g. 'IN', 'NOT IN'
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
    ] + parent::render() + [
      '#cache' => [
        'contexts' => $this->cacheContexts(),
        'tags' => $this->cacheTags(),
      ],
    ];
  }

  /**
   * Retrieve cache contexts based on by which entity fields the list can be filtered.
   *
   * @return array
   *   Array containing cache contexts of the form "url.query_args:ENTITY_FIELD_ID".
   *
   * @link https://www.drupal.org/docs/8/api/cache-api/cache-contexts
   */
  protected function cacheContexts() {
    $contexts = [];
    $context_base = 'url.query_args';
    foreach ($this->filterableFieldIds() as $field_id) {
      $contexts[] = sprintf('%s:%s', $context_base, $field_id);
      foreach (array_keys($this->queryOperands()) as $op)
        $contexts[] = sprintf('%s:%s__%s', $context_base, $field_id, $op);
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
    /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $field_manager */
    $field_manager = \Drupal::service('entity_field.manager');
    return array_keys($field_manager
      ->getFieldStorageDefinitions($this->getStorage()->getEntityTypeId()
      ));
  }

  /**
   * Retrieve cache tags.
   *
   * @return array
   *   Array of cache tags.
   *
   * @link https://www.drupal.org/docs/8/api/cache-api/cache-tags
   */
  protected function cacheTags() {
    return [];
  }

}
