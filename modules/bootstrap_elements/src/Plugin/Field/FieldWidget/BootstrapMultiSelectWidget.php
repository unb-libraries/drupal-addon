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

  public static function defaultSettings() {
    // Commented out settings are currently not supported.
    return parent::defaultSettings() + [
        // "enableHTML" => FALSE,
        "enableClickableOptGroups" => TRUE,
        "enableCollapsibleOptGroups" => TRUE,
        "collapseOptGroupsByDefault" => FALSE,
        "disableIfEmpty" => TRUE,
        // "disabledText" => "",
        // "dropRight" => FALSE,
        // "dropUp" => FALSE,
        // "maxHeight" => "",
        // "onChange" => "",
        // "onInitialized" => "",
        // "onDropdownShow" => "",
        // "onDropdownHide" => "",
        // "onDropdownShown" => "",
        // "onDropdownHidden" => "",
        "buttonClass" => "",
        // "inheritClass" => TRUE,
        // "buttonContainer" => "",
        // "buttonWidth" => "100%",
        // "buttonText" => "",
        // "buttonTitle" => "",
        "nonSelectedText" => t("None"),
        // "nSelectedText" => "",
        "allSelectedText" => t("All"),
        "numberDisplayed" => 1,
        // "delimiterText" => ",",
        // "optionLabel" => "",
        // "optionClass" => "",
        "selectedClass" => "",
        "includeSelectAllOption" => TRUE,
        // "selectAllJustVisible" => "",
        "selectAllText" => t("All"),
        // "selectAllValue" => "",
        // "selectAllName" => "",
        "selectAllNumber" => FALSE,
        // "onSelectAll" => "",
        // "onDeselectAll" => "",
        "enableFiltering" => FALSE,
        "enableCaseInsensitiveFiltering" => TRUE,
        "enableFullValueFiltering" => FALSE,
        "filterBehavior" => "text",
        "filterPlaceholder" => t("Search"),
        "includeResetOption" => FALSE,
        // "includeResetDivider" => "",
        "resetText" => t("Reset"),
    ];
  }
}
