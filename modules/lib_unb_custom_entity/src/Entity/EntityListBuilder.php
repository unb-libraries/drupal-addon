<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityListBuilder as DefaultEntityListBuilder;

class EntityListBuilder extends DefaultEntityListBuilder {

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
    $query = $this->getEntityQuery()
      ->sort($this->entityType->getKey('id'));

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $query->pager($this->limit);
    }
    return $query->execute();
  }

  /**
   * Build the query to populate this entity list.
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   The query.
   */
  protected function getEntityQuery() {
    return $this->getStorage()->getQuery();
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
    return $this->entityType->getListCacheContexts();
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
    return $this->entityType->getListCacheTags();
  }

}
