<?php

namespace Drupal\grid_select\Element;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a form element for a set of checkboxes.
 *
 * Properties:
 * - #columns: An associative array of keys and values.
 * - #rows: An associative array of keys and values.
 *
 * The product of the keys of #columns and #rows create
 * the value for each cell of the grid.
 *
 * Usage example:
 * @code
 * $form['high_school']['tests_taken'] = array(
 *   '#type' => 'grid_select',
 *   '#columns' => array('SAT' => $this->t('Saturday'), 'SUN' => $this->t('Sunday')),
 *   '#rows' => array('AM' => $this->t('Morning'), 'PM' => $this->t('Afternoon')),
 *   ...
 * );
 * @endcode
 *
 * @FormElement("grid_select")
 */
class GridSelect extends FormElement {

  /**
   * {@inheritDoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#tree' => TRUE,
      '#column_header' => [],
      '#row_header' => [],
      '#column_select' => TRUE,
      '#row_select' => TRUE,
      '#process' => [
        [$class, 'processGrid'],
      ],
      '#element_validate' => [
        [$class, 'validateGrid'],
      ],
      '#theme_wrappers' => ['grid_select'],
    ];
  }

  /**
   * {@inheritDoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    if (!$input) {
      $element += ['#default_value' => []];
      $value = array_filter($element['#default_value']);
      return array_keys(array_combine($value, $value));
    }
    else {
      return is_array($input) ? array_combine($input, $input) : [];
    }
  }

  /**
   * Processes a checkboxes form element.
   */
  public static function processGrid(&$element, FormStateInterface $form_state, &$complete_form) {
    $element['#tree'] = TRUE;

    foreach ($element['#rows'] as $rid => $row_label) {
      $element['#legend']['y'][$rid] = [
        '#type' => 'checkbox',
        '#title' => $row_label,
        '#attributes' => [
          'class' => [
            'row-select',
          ],
          'data-row' => $rid,
        ],
      ];
      foreach ($element['#columns'] as $cid => $column_label) {
        $element['#legend']['x'][$cid] = [
          '#type' => 'checkbox',
          '#title' => $column_label,
          '#attributes' => [
            'class' => [
              'column-select',
            ],
            'data-column' => $cid,
          ],
        ];

        $key = "{$rid}-{$cid}";
        $element[$key] = [
          '#type' => 'checkbox',
          '#title' => '',
          '#return_value' => $key,
          '#default_value' => in_array($key, $element['#value']) ? $key : NULL,
          '#attributes' => [
            'class' => [
              'cell-select',
            ],
            'data-row' => $rid,
            'data-column' => $cid,
          ],
        ];
      }
    }

    $element['#attributes']['class'][] = 'grid-select';
    $element['#attached']['library'][] = 'grid_select/gridselect';

    return $element;
  }

  /**
   * Element validate callback.
   *
   * @param array $element
   *   The grid element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $form
   *   The complete form array.
   */
  public static function validateGrid(&$element, FormStateInterface $form_state, array &$form) {
    $value = NestedArray::getValue($form_state->getValues(), $element['#parents']);
    $form_state->setValueForElement($element, array_values(array_filter($value)));
  }

}
