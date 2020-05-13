<?php

namespace Drupal\bootstrap_elements\Element;

use Drupal\Core\Render\Element\Select;
use Drupal\Core\Render\Annotation\FormElement;

/**
 * Renders a form "select" element containing entities of a given type as its options.
 *
 * Properties:
 *   - #entity_type: (string) ID of the entity type which the select options will be populated with.
 *   - #bundle: (string) limit the select options to the given bundle value.
 *
 * Usage example:
 * @code
 * $form['entity'] = [
 *   '#type' => 'entity_select',
 *   '#title' => $this->t('Entity'),
 *   '#entity_type' => 'node',
 *   '#bundle' => 'post'
 * ];
 * @endcode
 *
 * @FormElement("bootstrap_multiselect")
 */
class BootstrapMultiSelect extends Select {

  public function getInfo() {
    $info = parent::getInfo();
    $info['#attached']['library'][] = 'bootstrap_elements/bootstrap-multiselect';
    return $info;
  }

}