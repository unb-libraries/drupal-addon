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

  /**
   * Merge the state arrays of the given elements.
   *
   * @param array $element1
   *   The render into which to merge.
   * @param array $element2
   *   The render array to merge.
   * @param string $conjunction
   *   (optional) Whether to merge multiple rules for
   *   one state using 'and' (default) or 'or' conjunction.
   *
   * @return array
   *   The first element which '#states' array contains all its previous
   *   rules in addition to those of $element2's rules that it did not
   *   already define itself.
   */
  public static function mergeElementStates(array &$element1, array $element2, $conjunction = 'and') {
    $states1 = array_key_exists('#states', $element1)
      ? $element1['#states']
      : [];
    $states2 = array_key_exists('#states', $element2)
      ? $element2['#states']
      : [];

    $element1['#states'] = ElementState::mergeStates($states1, $states2, $conjunction);
    return $element1;
  }

}
