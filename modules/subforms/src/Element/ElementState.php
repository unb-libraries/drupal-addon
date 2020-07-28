<?php

namespace Drupal\subforms\Element;

/**
 * Provides helper methods for form state arrays.
 *
 * @package Drupal\subforms\Element
 */
class ElementState {

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
  public static function addState(array &$states, $state, array $rules, $conjunction = self::CONJUNCT_AND) {
    $states = static::mergeStates($states, [
      $state => $rules,
    ], $conjunction);
    return $states;
  }

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
  public static function removeState(array &$states, $state) {
    if (array_key_exists($state, $states)) {
      unset($states[$state]);
    }
    return $states;
  }

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
  public static function mergeStates(array &$states1, array $states2, $conjunction = self::CONJUNCT_AND) {
    foreach ($states2 as $state => $rules) {
      if (!array_key_exists($state, $states1)) {
        $states1[$state] = $rules;
      }
      else {
        $states1[$state] = static::mergeRules($states1[$state], $rules);
      }
    }
    return $states1;
  }

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
  public static function mergeRules(array &$rules1, array $rules2, $conjunction = self::CONJUNCT_AND) {
    if (static::isComplex($rules1) && static::isComplex($rules2)) {
      $rules1 = static::mergeComplexRules($rules1, $rules2, $conjunction);
    }
    elseif (static::isComplex($rules1)) {
      $rules1 = static::mergeSimpleAndComplexRules($rules1, $rules2, $conjunction);
    }
    elseif (static::isComplex($rules2)) {
      $rules1 = static::mergeSimpleAndComplexRules($rules2, $rules1, $conjunction);
    }
    else {
      $rules1 = static::mergeSimpleRules($rules1, $rules2, $conjunction);
    }
    return $rules1;
  }

  /**
   * Whether the given array defines a set of complex or simple rules.
   *
   * @param array $rules
   *   An array of rules.
   *
   * @return bool
   *   TRUE if the given array is a definition of complex rules.
   *   FALSE if the given array is a definition of simple rules.
   */
  public static function isComplex(array $rules) {
    if (!empty($rules)) {
      $complex = is_int(array_keys($rules)[0]);
    }
    else {
      $complex = FALSE;
    }
    return $complex;
  }

  /**
   * Merge two arrays of simple state rules.
   *
   * A "simple" rules array is an array where each entry
   * assigns a condition array to a string selector.
   *
   * Example:
   * [
   *   'input[name="input-1"] => [
   *     'value' => 1,
   *   ],
   *   'input[name="input-2"] => [
   *     'value' => 1,
   *   ],
   * ];
   *
   * @param array $simple1
   *   The array of rules to merge into.
   * @param array $simple2
   *   The array of rules to merge.
   * @param string $conjunction
   *   The conjunction to use when merging rules,
   *   either 'and' (default) or 'or'.
   *
   * @return array
   *   A rules array containing all rules
   *   from $simple1 and those rules from $simple2,
   *   that do not already exist in $simple1.
   */
  public static function mergeSimpleRules(array &$simple1, array $simple2, $conjunction = self::CONJUNCT_AND) {
    if ($conjunction === self::CONJUNCT_AND) {
      foreach ($simple2 as $selector => $condition) {
        if (!array_key_exists($selector, $simple1)) {
          $simple1[$selector] = $condition;
        }
      }
    }
    else {
      $complex1 = static::simpleToComplex($simple1);
      $complex2 = static::simpleToComplex($simple2);
      $simple1 = static::mergeComplexRules($complex1, $complex2, self::CONJUNCT_OR);
    }
    return $simple1;
  }

  /**
   * Convert the given array of simple rules to an array of complex rules.
   *
   * @param array $simple
   *   An array of simple rules.
   *
   * @return array
   *   An array of complex rules containing all simple
   *   rules conjunct with 'and'.
   */
  public static function simpleToComplex(array $simple) {
    $complex = [];
    foreach ($simple as $selector => $condition) {
      $complex[] = [$selector => $condition];
      $last_selector = array_keys($simple)[count($simple) - 1];
      if ($selector !== $last_selector) {
        $complex[] = self::CONJUNCT_AND;
      }
    }
    return $complex;
  }

  /**
   * Merge two arrays of complex state rules.
   *
   * A "complex" rules array is an array where each entry
   * is either an array which wraps a condition and a string selector
   * or a conjunction string, either 'and' or 'or'.
   *
   * Example:
   * [
   *   [
   *     'input[name="input-1"] => [
   *       'value' => 1,
   *     ],
   *   ], 'or',
   *   [
   *     'input[name="input-1"] => [
   *       'value' => 2,
   *     ],
   *   ],
   * ];
   *
   * @param array $complex1
   *   The rules array to merge into.
   * @param array $complex2
   *   The rules array to merge.
   * @param string $conjunction
   *   The conjunction to use when merging rules,
   *   either 'and' (default) or 'or'.
   * @return array
   *   A complex rules array containing all rules
   *   from $complex1 and those rules from $complex2,
   *   that do not already exist in $complex1.
   */
  public static function mergeComplexRules(array &$complex1, array $complex2, $conjunction = self::CONJUNCT_AND) {
    $complex1 = [
      [$complex1],
      $conjunction,
      [$complex2],
    ];
    return $complex1;
  }

  /**
   * Merge a complex and a simple rules array.
   *
   * @param array $complex
   *   The complex rules array.
   * @param array $simple
   *   The simple rules array.
   * @param string $conjunction
   *   The conjunction to use when merging rules,
   *   either 'and' (default) or 'or'.
   *
   * @return array
   *   A complex rules array containing $complex's rules grouped
   *   int one and $simple's rules grouped into another group
   *   of rules.
   */
  public static function mergeSimpleAndComplexRules(array &$complex, array $simple, $conjunction = self::CONJUNCT_AND) {
    $complex2 = static::simpleToComplex($simple);
    $complex = static::mergeComplexRules($complex, $complex2);
    return $complex;
  }

}
