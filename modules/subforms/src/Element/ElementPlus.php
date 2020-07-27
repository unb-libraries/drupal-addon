<?php

namespace Drupal\subforms\Element;

use Drupal\Core\Render\Element;

/**
 * Extension of Drupal's default Element class.
 *
 * @package Drupal\subforms\Element
 */
class ElementPlus extends Element {

  /**
   * Whether the given element is required.
   *
   * @param array $element
   *   The element.
   *
   * @return bool
   *   TRUE if the given element or any of its children
   *   sets #required = TRUE. FALSE otherwise.
   */
  public static function isRequiredElement(array $element) {
    return isset($element['#required']) && $element['#required'];
  }

  /**
   * Returns the required children of an element.
   *
   * @param array $elements
   *   The parent element.
   *
   * @return array
   *   The array keys of the element's required children.
   */
  public static function getRequiredChildren(array $elements) {
    $required_child_ids = [];
    foreach (static::children($elements) as $child_id) {
      $child = $elements[$child_id];
      if (static::isRequiredElement($child)) {
        $required_child_ids[] = $child_id;
      }
    }
    return $required_child_ids;
  }

  /**
   * Whether the given element defines any "required" states.
   *
   * @param array $element
   *   The element.
   *
   * @return bool
   *   TRUE if the given element includes a "required" condition
   *   in its list of "#states". FALSE otherwise.
   */
  public static function isConditionallyRequired(array $element) {
    return array_key_exists('#states', $element)
      && array_key_exists('required', $element['#states']);
  }

}
