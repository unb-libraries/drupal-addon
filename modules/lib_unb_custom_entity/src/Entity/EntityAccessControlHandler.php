<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler as DefaultEntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides access checking for entity operations.
 *
 * Supports any operation for which a permission of
 * the form "OPERATION ENTITY_TYPE entities" is defined.
 *
 * Example:
 * A route which requires "_entity_access": "node.view"
 * will be granted access to any user who has the
 * "view node entities" permission.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
class EntityAccessControlHandler extends DefaultEntityAccessControlHandler {

  /**
   * Performs access checks.
   *
   * Overrides DefaultEntityAccessControlHandler::checkAccess. This
   * checks for any permission of the form "OPERATION ENTITY_TYPE entities"
   * for the given user account.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity for which to check access.
   * @param string $operation
   *   The entity operation. Usually one of 'view', 'view', 'edit',
   *   'delete', or 'list'.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user for which to check access.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    $access = parent::checkAccess($entity, $operation, $account);
    if (!$access->isForbidden()) {
      $account = $this->prepareUser($account);

      $entity_type_id = $entity->getEntityTypeId();
      $required_permission = "$operation $entity_type_id entities";
      $access = AccessResult::allowedIfHasPermission($account, $required_permission); // TODO: cache access checking.
    }

    return $access;
  }

  /**
   * Performs create access checks.
   *
   * Overrides DefaultEntityAccessControlHandler::checkCreateAccess. This
   * performs the same access check on 'create' as on any other
   * operation.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user for which to check access.
   * @param array $context
   *   An array of key-value pairs to pass additional context when needed.
   * @param string|null $entity_bundle
   *   (optional) The bundle of the entity. Required if the entity supports
   *   bundles, defaults to NULL otherwise.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    $access = parent::checkCreateAccess($account, $context, $entity_bundle);
    if (!$access->isForbidden() && $entity_type_id = $context['entity_type_id']) {
      $required_permission = "create $entity_type_id entities";
      $access = AccessResult::allowedIfHasPermission($account, $required_permission);
    }
    return $access;
  }
}