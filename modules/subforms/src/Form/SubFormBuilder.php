<?php

namespace Drupal\subforms\Form;

use Drupal\Core\Form\FormBuilder;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

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
    $parents = $form_state->getBuildInfo()['parents'];
    $form['#parents'] = array_merge($form['#parents'], $parents);
    $form['#tree'] = FALSE;
    parent::prepareForm($form_id, $form, $form_state);
  }

  /**
   * {@inheritDoc}
   */
  public function doBuildForm($form_id, &$element, FormStateInterface &$form_state) {
    foreach (Element::children($element) as $key) {
      if (!isset($element[$key]['#tree'])) {
        $element[$key]['#tree'] = $element['#tree'];
      }

      $has_children = !empty(Element::children($element[$key]));
      if (!isset($element[$key]['#parents'])) {
        if (!$element[$key]['#tree'] && $has_children) {
          $element[$key]['#parents'] = $element['#parents'];
        }
        else {
          $element[$key]['#parents'] = array_merge($element['#parents'], [$key]);
        }
      }
    }
    return parent::doBuildForm($form_id, $element, $form_state);
  }

}
