<?php

namespace Drupal\lib_unb_custom_entity\Entity\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider;
use Symfony\Component\Routing\Route;

class HtmlRouteProvider extends DefaultHtmlRouteProvider {

  /**
   * {@inheritDoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $routes = parent::getRoutes($entity_type);
    $entity_type_id = $entity_type->id();

    if ($delete_all_route = $this->getDeleteAllFormRoute($entity_type)) {
      $routes->add("entity.{$entity_type_id}.delete_all", $delete_all_route);
    }

    foreach ($routes->all() as $route) {
      $route->setRequirement('_module_dependencies', $entity_type->getProvider());
    }

    return $routes;
  }

  /**
   * Gets the collection route.
   *
   * Overrides DefaultHtmlRouteProvider::getCollectionRoute.
   * This removes the admin_permission requirement from the route
   * and replaces it by checking for "list ENTITY_TYPE entities" permission.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getCollectionRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('collection') && $entity_type->hasListBuilderClass()) {
      $entity_type_id = $entity_type->id();
      /** @var \Drupal\Core\StringTranslation\TranslatableMarkup $label */
      $label = $entity_type->getCollectionLabel();

      $route = new Route($entity_type->getLinkTemplate('collection'));
      $route
        ->addDefaults([
          '_entity_list' => $entity_type_id,
          '_title' => $label->getUntranslatedString(),
          '_title_arguments' => $label->getArguments(),
          '_title_context' => $label->getOption('context'),
        ])
        ->setRequirement('_permission', "list {$entity_type_id} entities");

      return $route;
    }
    return NULL;
  }

  /**
   * Gets the "delete-all" route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getDeleteAllFormRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('delete-all-form')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('delete-all-form'));
      $route->addDefaults([
        '_form' => $entity_type->getFormClass('delete-all'),
        '_title' => t('Delete all ' . $entity_type->getPluralLabel()),
        'entity_type_id' => $entity_type_id,
      ])
        ->setRequirement('_permission', "delete all {$entity_type_id} entities");

      return $route;
    }
  }

}
