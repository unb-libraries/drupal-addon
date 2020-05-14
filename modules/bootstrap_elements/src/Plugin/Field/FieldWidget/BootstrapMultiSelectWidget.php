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

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $default_settings = static::defaultSettings();

    $form['button_style'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Button Style'),
      'allSelectedText' => [
        '#type' => 'textfield',
        '#title' => $this->t('All-selected text'),
        '#description' => $this->t('The text displayed when all options are selected.'),
        '#default_value' => $this->getSetting('allSelectedText') ?: $default_settings['allSelectedText'],
      ],
      'nonSelectedText' => [
        '#type' => 'textfield',
        '#title' => $this->t('Non-selected text'),
        '#description' => $this->t('The text displayed when no option is selected.'),
        '#default_value' => $this->getSetting('nonSelectedText') ?: $default_settings['nonSelectedText'],
      ],
      'numberDisplayed' => [
        '#type' => 'number',
        '#title' => $this->t('Number of selected labels to display'),
        '#description' => $this->t('If the number of selected options is higher than this number, the text on the button switches to "x selected"'),
        '#default_value' => $this->getSetting('numberDisplayed') ?: $default_settings['numberDisplayed'],
      ],
      'selectAllNumber' => [
        '#type' => 'radios',
        '#title' => $this->t('Display "all" number'),
        '#description' => $this->t('If enabled and all options are selected, the number of selected options wil be displayed.'),
        '#options' => [
          0 => $this->t('No'),
          1 => $this->t('Yes'),
        ],
        '#default_value' => $this->getSetting('selectAllNumber') ?: $default_settings['selectAllNumber'],
      ],
      'disableIfEmpty' => [
        '#type' => 'radios',
        '#title' => $this->t('Disable if empty'),
        '#description' => $this->t('If enabled, the multiselect will be disabled if no options are given.'),
        '#options' => [
          0 => $this->t('No'),
          1 => $this->t('Yes'),
        ],
        '#default_value' => $this->getSetting('disableIfEmpty') ?: $default_settings['disableIfEmpty'],
      ],
      'buttonClass' => [
        '#type' => 'textfield',
        '#title' => $this->t('CSS class'),
        '#description' => $this->t('The class of the multiselect button. '),
        '#default_value' => $this->getSetting('buttonClass') ?: $default_settings['buttonClass'],
      ],
    ];

    $form['options'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Options'),
      'enableClickableOptGroups' => [
        '#type' => 'radios',
        '#title' => $this->t('Enable clickable option groups'),
        '#description' => $this->t('If enabled, option groups will be clickable, selecting all options within that group.'),
        '#options' => [
          0 => $this->t('No'),
          1 => $this->t('Yes'),
        ],
        '#default_value' => $this->getSetting('enableClickableOptGroups') ?: $default_settings['enableClickableOptGroups'],
      ],
      'enableCollapsibleOptGroups' => [
        '#type' => 'radios',
        '#title' => $this->t('Enable collapsible option groups'),
        '#description' => $this->t('If enabled, option groups will be collapsible.'),
        '#options' => [
          0 => $this->t('No'),
          1 => $this->t('Yes'),
        ],
        '#default_value' => $this->getSetting('enableCollapsibleOptGroups') ?: $default_settings['enableCollapsibleOptGroups'],
      ],
      'collapseOptGroupsByDefault' => [
        '#type' => 'radios',
        '#title' => $this->t('Collapse option groups by default'),
        '#description' => $this->t('If enabled and option groups are collapsible, option groups will be default be collapsed.'),
        '#options' => [
          0 => $this->t('No'),
          1 => $this->t('Yes'),
        ],
        '#default_value' => $this->getSetting('collapseOptGroupsByDefault') ?: $default_settings['collapseOptGroupsByDefault'],
      ],
      'selectedClass' => [
        '#type' => 'textfield',
        '#title' => $this->t('Selected class'),
        '#description' => $this->t('The class(es) applied on selected options. '),
        '#default_value' => $this->getSetting('selectedClass') ?: $default_settings['selectedClass'],
      ],
      'includeSelectAllOption' => [
        '#type' => 'radios',
        '#title' => $this->t('Include select-all option'),
        '#description' => $this->t('If enabled, the option list will contain an "all" option.'),
        '#options' => [
          0 => $this->t('No'),
          1 => $this->t('Yes'),
        ],
        '#default_value' => $this->getSetting('includeSelectAllOption') ?: $default_settings['includeSelectAllOption'],
      ],
      'selectAllText' => [
        '#type' => 'textfield',
        '#title' => $this->t('Select-all text'),
        '#description' => $this->t('The label for the "select-all" option.'),
        '#default_value' => $this->getSetting('selectAllText') ?: $default_settings['selectAllText'],
      ],
      'includeResetOption' => [
        '#type' => 'radios',
        '#title' => $this->t('Include "reset" option'),
        '#description' => $this->t('If enabled, the option list will contain a "reset" option.'),
        '#options' => [
          0 => $this->t('No'),
          1 => $this->t('Yes'),
        ],
        '#default_value' => $this->getSetting('includeResetOption') ?: $default_settings['includeResetOption'],
      ],
      'resetText' => [
        '#type' => 'textfield',
        '#title' => $this->t('Reset text'),
        '#description' => $this->t('The label for the "reset" option.'),
        '#default_value' => $this->getSetting('resetText') ?: $default_settings['resetText'],
      ],
    ];

    $form['filter'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Filter'),
      'enableFiltering' => [
        '#type' => 'radios',
        '#title' => $this->t('Enable filtering'),
        '#description' => $this->t('If enabled, a search filter will be included at the top of the option list.'),
        '#options' => [
          0 => $this->t('No'),
          1 => $this->t('Yes'),
        ],
        '#default_value' => $this->getSetting('enableFiltering') ?: $default_settings['enableFiltering'],
      ],
      'enableCaseInsensitiveFiltering' => [
        '#type' => 'radios',
        '#title' => $this->t('Enforce case-sensitive filtering'),
        '#description' => $this->t('If enabled, filtering is case-sensitive.'),
        '#options' => [
          0 => $this->t('Yes'),
          1 => $this->t('No'),
        ],
        '#default_value' => $this->getSetting('enableCaseInsensitiveFiltering') ?: $default_settings['enableCaseInsensitiveFiltering'],
      ],
      'enableFullValueFiltering' => [
        '#type' => 'radios',
        '#title' => $this->t('Filter method'),
        '#description' => $this->t('How to match filter text and options.'),
        '#options' => [
          0 => $this->t('Contains'),
          1 => $this->t('Starts with'),
        ],
        '#default_value' => $this->getSetting('enableFullValueFiltering') ?: $default_settings['enableFullValueFiltering'],
      ],
      'filterBehavior' => [
        '#type' => 'select',
        '#title' => $this->t('Filter behavior'),
        '#description' => $this->t('Whether to filter based on option labels, values, or both.'),
        '#options' => [
          'text' => $this->t('Labels'),
          'value' => $this->t('Values'),
          'both' => $this->t('Labels and Values'),
        ],
        '#default_value' => $this->getSetting('filterBehavior') ?: $default_settings['filterBehavior'],
      ],
      'filterPlaceholder' => [
        '#type' => 'textfield',
        '#title' => $this->t('The filter input placeholder'),
        '#description' => $this->t('The text displayed as a placeholder before any filter input has been made.'),
        '#default_value' => $this->getSetting('filterPlaceholder') ?: $default_settings['filterPlaceholder'],
      ],
    ];

    return $form;
  }

}
