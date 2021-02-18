<?php

namespace Drupal\lib_unb_custom_entity\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormHelper as CoreFormHelper;

/**
 * Provides form helpers in addition to Drupal's core FormHelper class.
 *
 * @package Drupal\lib_unb_custom_entity\Form
 */
class FormHelper extends CoreFormHelper {

  /**
   * Convert an array of entities into an array of entity labels.
   *
   * @param \Drupal\Core\Entity\EntityInterface[] $entities
   *   The entities.
   *
   * @return array
   *   An array of the form ENTITY_ID => ENTITY_LABEL.
   */
  public static function entityLabels(array $entities) {
    return array_map(function (EntityInterface $entity) {
      return $entity->label();
    }, $entities);
  }

}
