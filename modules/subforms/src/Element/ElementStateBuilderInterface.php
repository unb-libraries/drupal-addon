<?php

namespace Drupal\subforms\Element;

interface ElementStateBuilderInterface {

  const CONJUNCT_AND = 'and';
  const CONJUNCT_OR = 'or';

  /**
   * Add a state to the given array of states.
   *
   * @param array $states
   *   The array of state definitions to which to add a state.
   * @param string $state
   *   The state key.
   * @param array $rules
   *   An array of rules.
   * @param string $conjunction
   *   The conjunction to use when adding the state,
   *   either 'and' (default) or 'or'.
   *
   * @return array
   *   The modified element.
   */
  public function addState(array &$states, $state, array $rules, $conjunction = self::CONJUNCT_AND);
  /**
   * Remove the state with the given key from the given states array.
   *
   * @param array $states
   *   The states array.
   * @param string $state
   *   The state key.
   *
   * @return array
   *   The states array after the state with the
   *   given key has been removed.
   */
  public function removeState(array &$states, $state);

  /**
   * Merge the state arrays of the given elements.
   *
   * @param array $element1
   *   The render into which to merge.
   * @param array $element2
   *   The render array to merge.
   * @param bool $recursive
   *   Whether to recursively merge children's states.
   * @param string $conjunction
   *   (optional) Whether to merge multiple rules for
   *   one state using 'and' (default) or 'or' conjunction.
   *
   * @return array
   *   The first element which '#states' array contains all its previous
   *   rules in addition to those of $element2's rules that it did not
   *   already define itself.
   */
  public function mergeElementStates(array &$element1, array $element2, $conjunction = self::CONJUNCT_AND);

  /**
   * Merge two arrays of state definitions.
   *
   * @param array $states1
   *   The array of state definitions to merge into.
   * @param array $states2
   *   The array of state to merge.
   * @param string $conjunction
   *   The conjunction to use when merging arrays,
   *   either 'and' (default) or 'or'.
   *
   * @return array
   *   The first states array with all rules of
   *   of the other states array merged into it.
   */
  public function mergeStates(array &$states1, array $states2, $conjunction = self::CONJUNCT_AND);
  /**
   * Merge two arrays of state rules.
   *
   * @param array $rules1
   *   The array of rules to merge into.
   * @param array $rules2
   *   The array of rules to merge.
   * @param string $conjunction
   *   The conjunction to use when merging rules,
   *   either 'and' (default) or 'or'.
   *
   * @return array
   *   The first rules array with all rules of
   *   the other rules array merged into it.
   */
  public function mergeRules(array &$rules1, array $rules2, $conjunction = self::CONJUNCT_AND);

  /**
   * Whether the given element defines any "optional" states.
   *
   * @param array $element
   *   The element.
   *
   * @return bool
   *   TRUE if the given element includes a "optional" condition
   *   in its list of "#states". FALSE otherwise.
   */
  public function isConditionallyOptional(array $element);

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
  public function isConditionallyRequired(array $element);

}