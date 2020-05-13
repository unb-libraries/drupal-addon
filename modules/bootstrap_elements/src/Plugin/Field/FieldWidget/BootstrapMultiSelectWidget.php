<?php

namespace Drupal\bootstrap_elements\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsSelectWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Implementation of a Bootstrap-based, multi-select drop-down widget.
 *
 * @FieldWidget(
 *   id = "bootstrap_multiselect",
 *   label = @Translation("Bootstrap multi-select"),
 *   field_types = {
 *     "entity_reference",
 *     "list_integer",
 *     "list_float",
 *     "list_string"
 *   },
 *   multiple_values = TRUE
 * )
 */
class BootstrapMultiSelectWidget extends OptionsSelectWidget {

  use StringTranslationTrait;

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = array_merge(parent::formElement($items, $delta, $element, $form, $form_state), [
      '#type' => 'bootstrap_multiselect',
      '#default_value' => $this->getSelectedOptions($items),
      '#multiple' => TRUE,
      '#settings' => $this->getSettings(),
      '#attributes' => [
        'class' => [
          'multiselect',
        ],
      ],
    ]);
    return $element;
  }
}
