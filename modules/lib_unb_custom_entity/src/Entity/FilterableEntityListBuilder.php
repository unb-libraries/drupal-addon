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

}
