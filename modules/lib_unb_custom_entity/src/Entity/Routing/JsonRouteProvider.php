<?php

namespace Drupal\lib_unb_custom_entity\Entity\Routing;

use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\EntityRouteProviderInterface;
use Drupal\lib_unb_custom_entity\Controller\JsonController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provides JSON routes for entities.
 *
 * This provider creates routes based on an entity type's link templates.
 * It generates CANONICAL,EDIT, and DELETE routes based on the
 * 'canonical' template as well as CREATE and COLLECTION routes
 * based on the 'collection' template.
 *
 * @package Drupal\unb_locations\Entity\Routing
 */
class JsonRouteProvider implements EntityHandlerInterface, EntityRouteProviderInterface {

  protected const ROUTE_NAME_TEMPLATE = 'entity.{entity_type}.{action}.{format}';

  protected const PATH_CANONICAL = 'canonical';
  protected const PATH_CREATE = 'create';
  protected const PATH_EDIT = 'edit';
  protected const PATH_DELETE = 'delete';
  protected const PATH_COLLECTION = 'collection';

  protected const ACTION_VIEW = 'view';
  protected const ACTION_CREATE = 'add';
  protected const ACTION_EDIT = 'edit';
  protected const ACTION_DELETE = 'delete';
  protected const ACTION_LIST = 'list';

  protected const METHOD_GET = 'GET';
  protected const METHOD_POST = 'POST';
  protected const METHOD_PATCH = 'PATCH';
  protected const METHOD_DELETE = 'DELETE';

  protected const FORMAT = 'json';

  /**
   * {@inheritDoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static();
  }

  /**
   * {@inheritDoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $routes = new RouteCollection();

    // Canonical route.
    if ($entity_type->hasLinkTemplate(self::PATH_CANONICAL)) {
      $canonical_route = $this->getCanonicalRoute($entity_type);
      $canonical_name = $this->createRouteName($entity_type, self::ACTION_VIEW);
      $routes->add($canonical_name, $canonical_route);
    }

    // Create route.
    if ($entity_type->hasLinkTemplate(self::PATH_CREATE) || $entity_type->hasLinkTemplate(self::PATH_COLLECTION)) {
      $create_route = $this->getCreateRoute($entity_type);
      $create_name = $this->createRouteName($entity_type, self::ACTION_CREATE);
      $routes->add($create_name, $create_route);
    }

    // Update route.
    if ($entity_type->hasLinkTemplate(self::PATH_EDIT) || $entity_type->hasLinkTemplate(self::PATH_CANONICAL)) {
      $update_route = $this->getUpdateRoute($entity_type);
      $update_name = $this->createRouteName($entity_type, self::ACTION_EDIT);
      $routes->add($update_name, $update_route);
    }

    // Delete route.
    if ($entity_type->hasLinkTemplate(self::PATH_DELETE) || $entity_type->hasLinkTemplate(self::PATH_CANONICAL)) {
      $delete_route = $this->getDeleteRoute($entity_type);
      $delete_name = $this->createRouteName($entity_type, self::ACTION_DELETE);
      $routes->add($delete_name, $delete_route);
    }

    // Collection route.
    if ($entity_type->hasLinkTemplate(self::PATH_COLLECTION)) {
      $collection_route = $this->getCollectionRoute($entity_type);
      $collection_name = $this->createRouteName($entity_type, self::ACTION_LIST);
      $routes->add($collection_name, $collection_route);
    }

    return $routes;
  }

  /**
   * Create the 'canonical' route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to which the generate route applies.
   *
   * @return \Symfony\Component\Routing\Route|bool
   *   An instance of Route.
   */
  protected function getCanonicalRoute(EntityTypeInterface $entity_type) {
    return $this->getEntityRoute(
      $entity_type, self::ACTION_VIEW, self::METHOD_GET
    );
  }

  /**
   * Create the 'create' route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to which the generate route applies.
   *
   * @return \Symfony\Component\Routing\Route|bool
   *   An instance of Route.
   */
  protected function getCreateRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate(self::PATH_CREATE)) {
      $path = $entity_type->getLinkTemplate(self::PATH_CREATE);
    }
    elseif ($entity_type->hasLinkTemplate(self::PATH_COLLECTION)) {
      $path = $entity_type->getLinkTemplate(self::PATH_COLLECTION);
    }
    else {
      $path = '';
    }

    return $this->getEntityBaseRoute(
      $entity_type, self::ACTION_CREATE, self::METHOD_POST, $path
    );
  }

  /**
   * Create the 'update' route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to which the generate route applies.
   *
   * @return \Symfony\Component\Routing\Route|bool
   *   An instance of Route.
   */
  protected function getUpdateRoute(EntityTypeInterface $entity_type) {
    return $this->getEntityRoute(
      $entity_type, self::ACTION_EDIT, self::METHOD_PATCH
    );
  }

  /**
   * Create the 'delete' route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to which the generate route applies.
   *
   * @return \Symfony\Component\Routing\Route|bool
   *   An instance of Route.
   */
  protected function getDeleteRoute(EntityTypeInterface $entity_type) {
    return $this->getEntityRoute(
      $entity_type, self::ACTION_DELETE, self::METHOD_DELETE
    );
  }

  /**
   * Create the 'collection' route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to which the generate route applies.
   *
   * @return \Symfony\Component\Routing\Route|bool
   *   An instance of Route. If no route could be created, FALSE will be returned.
   */
  protected function getCollectionRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate(self::PATH_COLLECTION)) {
      $path = $entity_type->getLinkTemplate(self::PATH_COLLECTION);
    }
    else {
      $path = '';
    }
    return $this->getEntityBaseRoute(
      $entity_type, self::ACTION_LIST, self::METHOD_GET, $path
    );
  }

  /**
   * Create the base route for a single entity.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to which the generate route applies.
   * @param string $action
   *   The controller action to handle requests that match the route.
   * @param string $method
   *   The method the route responds to.
   * @param string $path
   *   The path the route should match.
   *
   * @return \Symfony\Component\Routing\Route
   *   An instance of Route.
   */
  protected function getEntityRoute(EntityTypeInterface $entity_type, $action = self::ACTION_VIEW, $method = self::METHOD_GET, $path = '') {
    if (!$path) {
      if ($entity_type->hasLinkTemplate(self::PATH_CANONICAL)) {
        $path = $entity_type->getLinkTemplate(self::PATH_CANONICAL);
      }
      else {
        $pluralized_entity_type = strtolower($entity_type->getPluralLabel());
        $path = sprintf('/%s/{%s}', $pluralized_entity_type, $entity_type->id());
      }
    }

    $route = $this->getEntityBaseRoute($entity_type, $action, $method, $path);
    $entity_type_id = $entity_type->id();
    $route->addRequirements([
      '_entity_access' => "{$entity_type_id}.{$action}",
      '_format' => self::FORMAT,
    ]);
//    $route->addOptions([
//      'parameters' => [
//        $entity_type_id => [
//          'type' => 'entity:' . $entity_type->id(),
//        ],
//      ],
//    ]);

    return $route;
  }

  /**
   * Create the base route for the entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to which the generate route applies.
   * @param string $action
   *   The controller action to handle requests that match the route.
   * @param string $method
   *   The method the route responds to.
   * @param string $path
   *   The path the route should match.
   *
   * @return \Symfony\Component\Routing\Route
   *   An instance of Route.
   */
  protected function getEntityBaseRoute(EntityTypeInterface $entity_type, $action = self::ACTION_VIEW, $method = self::METHOD_GET, $path = '') {
    if (!$path) {
      $path = sprintf('/%s', strtolower($entity_type->getPluralLabel()));
    }

    $route = new Route($path);
    $route->addDefaults([
      '_controller' => $this->getControllerAction($action),
    ]);
    $route->setMethods([$method]);

    return $route;
  }

  /**
   * Retrieve a controller action.
   *
   * @param $action
   *   The action to retrieve.
   *
   * @return string
   *   A string of the form CONTROLLER_CLASS::ACTION.
   */
  protected function getControllerAction($action) {
    return JsonController::class . '::' . $action;
  }

  /**
   * Create a route identifier based on the given entity type and route type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to which the generate route applies.
   * @param string $action
   *   The action to which the route shall respond.
   *
   * @return string
   *   A string.
   */
  protected function createRouteName(EntityTypeInterface $entity_type, $action) {
    $to_be_replaced = [
      '{entity_type}',
      '{action}',
      '{format}'
    ];
    $replace_with = [
      $entity_type->id(),
      $action,
      self::FORMAT,
    ];

    return str_replace($to_be_replaced, $replace_with, self::ROUTE_NAME_TEMPLATE);
  }

}
