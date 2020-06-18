<?php

namespace Drupal\lib_unb_custom_entity\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Checkboxes;

/**
 * An enhanced "checkboxes" element which provides instances of a given entity type as its selectable options.
 *
 * @see \Drupal\lib_unb_custom_entity\Element\EntityFormOptionsTrait
 *
 * @FormElement("entity_checkboxes")
 */
class EntityCheckboxes extends Checkboxes {

  use EntityFormOptionsTrait;

  /**
   * {@inheritDoc}
   */
  public function getInfo() {
    return parent::getInfo() + $this->entityInfo();
  }

  /**
   * {@inheritDoc}
   */
  public static function processCheckboxes(&$element, FormStateInterface $form_state, &$complete_form) {
    $element['#options'] = self::buildEntityOptions($element) + $element['#options'];
    return parent::processCheckboxes($element, $form_state, $complete_form);
  }

}
