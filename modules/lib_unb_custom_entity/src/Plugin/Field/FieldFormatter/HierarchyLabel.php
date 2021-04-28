<?php

namespace Drupal\lib_unb_custom_entity\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\lib_unb_custom_entity\Entity\HierarchicalInterface;

/**
 * Plugin implementation of the 'string' formatter.
 *
 * @FieldFormatter(
 *   id = "hierarchy_label",
 *   label = @Translation("Hierarchy label"),
 *   field_types = {
 *     "string",
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 *
 * @package Drupal\lib_unb_custom_entity\Plugin\Field\FieldFormatter
 */
class HierarchyLabel extends StringFormatter {

  /**
   * {@inheritDoc}
   */
  protected function viewValue(FieldItemInterface $item) {
    $render = parent::viewValue($item);
    $value = $render['#context']['value'];

    $entity = $item->getEntity();
    if ($entity instanceof HierarchicalInterface) {
      $prefix = $this->viewPrefix($entity);
      $value = "{$prefix} {$value}";
    }

    $render['#context']['value'] = $value;
    return $render;
  }

  /**
   * Build the prefix that indicates the nesting level within the hierarchy.
   *
   * @param \Drupal\lib_unb_custom_entity\Entity\HierarchicalInterface $entity
   *   A hierarchical entity.
   *
   * @return string
   *   A string.
   */
  protected function viewPrefix(HierarchicalInterface $entity) {
    $prefix = '';
    while ($entity->getSuperior()) {
      $prefix .= '---';
      $entity = $entity->getSuperior();
    }
    return $prefix;
  }

}
