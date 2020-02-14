<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityListBuilder;

/**
 * Build a listing of entities that can be filtered by HTTP GET parameters.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
class FilterableEntityListBuilder extends EntityListBuilder {

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
      // TODO: Allow not only filtering by "equals".
      $query->condition($param, $value, '=');
    }
    return $query;
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
    return parent::render() + [
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
    foreach ($this->filterableFieldIds() as $field_id) {
      $contexts[] = "url.query_args:{$field_id}";
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
