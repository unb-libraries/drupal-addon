<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder as DefaultEntityListBuilder;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Routing\RouteMatch;
use Drupal\Core\Url;

/**
 * Enhances Drupal's default EntityListBuilder implementation.
 */
class EntityListBuilder extends DefaultEntityListBuilder {

  /**
   * Whether the rendered list should be paginated.
   *
   * @var bool
   */
  protected $paginate = TRUE;

  /**
   * Whether the rendered list should be paginated.
   *
   * @return bool
   *   TRUE if pagination is activated and set to a limit > 0. FALSE otherwise.
   */
  protected function paginate() {
    return $this->limit() > 0 && $this->paginate;
  }

  /**
   * Enable pagination.
   */
  public function enablePagination() {
    $this->paginate = TRUE;
  }

  /**
   * Disable pagination.
   */
  public function disablePagination() {
    $this->paginate = FALSE;
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
    $query = $this->getEntityQuery();

    $this->sort($query);
    if ($this->paginate()) {
      $query->pager($this->limit());
    }

    return $query->execute();
  }

  /**
   * Sort the loaded entities.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   *   The entity query.
   */
  private function sort(QueryInterface &$query) {
    foreach ($this->sortKeys() as $key => $value) {
      if (is_int($key)) {
        $field_id = $value;
        $direction = 'ASC';
      }
      else {
        $field_id = $key;
        $direction = $value;
      }
      $query->sort($field_id, $direction);
    }
  }

  /**
   * Retrieve the sort keys.
   *
   * @return array
   *   Array of string. Each entry must correspond
   *   to a field of the entity type.
   */
  protected function sortKeys() {
    return [
      $this->getEntityType()->getKey('id'),
    ];
  }

  /**
   * Limit the number of rows.
   *
   * @return false|int
   *   An integer, or FALSE if no
   *   limit has been defined.
   */
  protected function limit() {
    return $this->limit;
  }

  /**
   * Retrieve the total number of entities.
   *
   * @return int
   *   An integer >= 0.
   */
  public function getCount() {
    return $this->getEntityQuery()
      ->count()
      ->execute();
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
    $build = parent::render();

    if (!$this->hasOperations($build['table']['#rows'])) {
      unset($build['table']['#header']['operations']);
      foreach ($build['table']['#rows'] as $index => $row) {
        unset($build['table']['#rows'][$index]['operations']);
      }
    }

    return $build + [
      'actions' => $this->actions(),
      '#cache' => [
        'contexts' => $this->cacheContexts(),
        'tags' => $this->cacheTags(),
      ],
    ];
  }

  /**
   * Whether any of the given rows contains a non-empty 'operations' column.
   *
   * @param array $rows
   *   An array of render arrays (table rows).
   *
   * @return bool
   *   TRUE if at least one row contains a non-empty 'operations' column.
   *   FALSE otherwise.
   */
  private function hasOperations(array $rows) {
    foreach ($rows as $row) {
      if (!empty($row['operations']['data'])) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function buildOperations(EntityInterface $entity) {
    $operations = parent::buildOperations($entity);
    if (!empty($operations['#links'])) {
      return $operations;
    }
    return [];
  }

  /**
   * Build the action buttons to appear on top of the page.
   *
   * @return array
   *   A render array (type: "container").
   */
  protected function actions() {
    $actions = [
      '#type' => 'container',
      '#weight' => -99,
    ];
    if ($create_action = $this->buildCreateAction()) {
      $actions['add'] = $create_action;
    }
    if ($delete_all_action = $this->buildDeleteAllAction()) {
      $actions['delete'] = $delete_all_action;
    }
    return $actions;
  }

  /**
   * Build a "create" action.
   *
   * @return array
   *   A render array (type: "link").
   */
  protected function buildCreateAction() {
    if ($add_template = $this->getEntityType()->getLinkTemplate('add-form')) {
      if (!empty($routes = $this->routeProvider()->getRoutesByPattern($add_template)->all())) {
        $add_route_name = array_keys($routes)[0];
        $add_route = $routes[$add_route_name];
        $route_match = new RouteMatch($add_route_name, $add_route);
        if ($this->createAccessCheck()->access($add_route, $route_match, $this->currentUser())->isAllowed()) {
          return [
            '#type' => 'link',
            '#title' => $this->t('Add @entity', [
              '@entity' => $this->getEntityType()->getSingularLabel(),
            ]),
            '#url' => Url::fromRoute(array_keys($routes)[0]),
          ];
        }
      }
    }
    return [];
  }

  /**
   * Build a "delete-all" action.
   *
   * @return array
   *   A render array (type: "link").
   */
  protected function buildDeleteAllAction() {
    if ($delete_all_template = $this->getEntityType()->getLinkTemplate('delete-all-form')) {
      $routes = $routes = $this->routeProvider()->getRoutesByPattern($delete_all_template)->all();
      if (!empty($routes) && $this->getCount() > 0) {
        $delete_all_route_name = array_keys($routes)[0];
        $delete_all_route = $routes[$delete_all_route_name];
        $required_permission = $delete_all_route->getRequirement('_permission');
        if ($this->currentUser()->hasPermission($required_permission)) {
          return [
            '#type' => 'link',
            '#title' => $this->t('Delete all @entities', [
              '@entities' => strtolower($this->getEntityType()->getPluralLabel()),
            ]),
            '#url' => Url::fromRoute($delete_all_route_name),
            '#button_type' => 'danger',
          ];
        }
      }
    }
    return [];
  }

  /**
   * Retrieve cache contexts.
   *
   * @return array
   *   Array of strings.
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

  /**
   * The route provider interface.
   *
   * @return \Drupal\Core\Routing\RouteProviderInterface
   *   A route provider object.
   */
  protected function routeProvider() {
    /** @var \Drupal\Core\Routing\RouteProviderInterface $route_provider */
    $route_provider = \Drupal::service('router.route_provider');
    return $route_provider;
  }

  /**
   * The currently logged-in user.
   *
   * @return \Drupal\Core\Session\AccountProxyInterface
   *   An account proxy object.
   */
  protected function currentUser() {
    return \Drupal::currentUser();
  }

  /**
   * Retrieve the "create" access check service.
   *
   * @return \Drupal\Core\Entity\EntityCreateAccessCheck
   *   An access check object.
   */
  protected function createAccessCheck() {
    /** @var \Drupal\Core\Entity\EntityCreateAccessCheck $access_check */
    $access_check = \Drupal::service('access_check.entity_create');
    return $access_check;
  }

}
