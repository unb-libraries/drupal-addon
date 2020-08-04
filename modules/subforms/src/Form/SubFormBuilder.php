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
    $form['#parents'] = isset($form['#parents'])
      ? array_merge($form['#parents'], $parents)
      : $parents;
    $form['#tree'] = FALSE;
    parent::prepareForm($form_id, $form, $form_state);
    $form['form_id']['#parents'] = array_merge($parents, $form['form_id']['#parents']);

    $form['form_build_id']['#parents'] = array_merge($parents, $form['form_build_id']['#parents']);
    $form['form_build_id']['#name'] = '';
    foreach ($form['form_build_id']['#parents'] as $parent) {
      if (empty($form['form_build_id']['#name'])) {
        $form['form_build_id']['#name'] = $parent;
      }
      else {
        $form['form_build_id']['#name'] .= "[{$parent}]";
      }
    }
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
