<?php

namespace Drupal\subforms\Form;

use Drupal\Core\Form\FormBuilder;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\subforms\Element\ElementPlus;
use Drupal\subforms\Element\ElementState;

/**
 * Provides form building and processing of integrated sub-forms.
 *
 * @package Drupal\subforms\Form
 */
class SubFormBuilder extends FormBuilder {

  /**
   * {@inheritDoc}
   */
  public function prepareForm($form_id, &$form, FormStateInterface &$form_state) {
    $build_info = $form_state->getBuildInfo();
    $form['#parents'] = isset($form['#parents'])
      ? array_merge($form['#parents'], $build_info['parents'])
      : $build_info['parents'];
    $form['#tree'] = FALSE;
    if (array_key_exists('states', $build_info)) {
      $form['#states'] = $build_info['states'];
    }

    parent::prepareForm($form_id, $form, $form_state);
  }

  /**
   * {@inheritDoc}
   */
  public function doBuildForm($form_id, &$element, FormStateInterface &$form_state) {
    foreach (Element::children($element) as $key) {
      $has_children = !empty(Element::children($element[$key]));

      if (!$form_state->isProcessingInput()) {
        if (!isset($element[$key]['#tree'])) {
          $element[$key]['#tree'] = $element['#tree'];
        }

        if (!isset($element[$key]['#parents'])) {
          if (!$element[$key]['#tree'] && $has_children) {
            $element[$key]['#parents'] = $element['#parents'];
          } else {
            $element[$key]['#parents'] = array_merge($element['#parents'], [$key]);
          }
        }
      }

      if (array_key_exists('#type', $element[$key]) && array_key_exists('#states', $element)) {
        ElementPlus::mergeElementStates($element[$key], $element);
        if ((ElementPlus::isConditionallyRequired($element) || ElementPlus::isConditionallyOptional($element[$key])) && (ElementPlus::isRequiredElement($element[$key]) || $has_children)) {
          // if the element is conditionally required, convert required children or children with children
          // into conditionally required children.
          if (isset($element[$key]['#required'])) {
            unset($element[$key]['#required']);
          }
        }
      }
    }

    return parent::doBuildForm($form_id, $element, $form_state);
  }

}
