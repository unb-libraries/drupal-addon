<?php

namespace Drupal\subforms\Element;

/**
 * Provides helper methods for form state arrays.
 *
 * @package Drupal\subforms\Element
 */
class ElementState {

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
  public static function mergeStates(array $states1, array $states2, $conjunction = 'and') {
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
  public static function mergeRules(array $rules1, array $rules2, $conjunction = 'and') {
    return $rules1;
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
  public static function mergeSimpleRules(array $simple1, array $simple2, $conjunction = 'and') {

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
    return $simple;
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
  public static function mergeComplexRules(array $complex1, array $complex2, $conjunction = 'and') {
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
  public static function mergeSimpleAndComplexRules(array $complex, array $simple, $conjunction = 'and') {
    return $complex;
  }

}
