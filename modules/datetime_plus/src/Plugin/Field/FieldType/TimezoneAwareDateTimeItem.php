<?php

namespace Drupal\datetime_plus\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\datetime_plus\Datetime\TimezoneAwareDateTimeComputed;
use Drupal\datetime_plus\Plugin\TimeZoneResolver\DateTimeZoneResolverTrait;

/**
 * Plugin implementation of the 'datetime_plus' field type.
 *
 * @FieldType(
 *   id = "datetime_timezone",
 *   label = @Translation("Date (custom timezone)"),
 *   description = @Translation("Create and store timezone-customizable date values."),
 *   default_widget = "datetime_timezone",
 *   default_formatter = "datetime_default",
 *   list_class = "\Drupal\datetime\Plugin\Field\FieldType\DateTimeFieldItemList",
 *   constraints = {"DateTimeFormat" = {}}
 * )
 */
class TimezoneAwareDateTimeItem extends DateTimeItem implements TimezoneAwareDateTimeItemInterface {

  use DateTimeZoneResolverTrait;

  /**
   * {@inheritDoc}
   */
  public function __construct(DataDefinitionInterface $definition, $name = NULL, TypedDataInterface $parent = NULL) {
    // @todo Replace by proper dependency injection once FieldType plugins support it.
    self::$dateTimeZoneResolverManager = \Drupal::service('plugin.manager.timezone_resolver');
    parent::__construct($definition, $name, $parent);
  }

  /**
   * {@inheritDoc}
   */
  public function getTimeZone() {
    return $this->getDateTime()
      ->getTimezone();
  }

  /**
   * {@inheritDoc}
   */
  public function getDateTime() {
    return $this->date;
  }

  /**
   * {@inheritDoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $properties['date']
      ->setClass(TimezoneAwareDateTimeComputed::class)
      ->setSetting('timezone', static::resolve(
        $field_definition->getSetting('timezone'),
        $field_definition->getSetting('resolver_settings')
      ));

    return $properties;
  }

  /**
   * {@inheritDoc}
   */
  public static function defaultFieldSettings() {
    return [
      'timezone' => 'user',
      'resolver_settings' => [],
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritDoc}
   */
  public function fieldSettingsForm(array $element, FormStateInterface $form_state) {
    $element = parent::fieldSettingsForm($element, $form_state);

    $element['timezone'] = [
      '#type' => 'select',
      '#title' => $this->t('Timezone'),
      '#descriptions' => $this->t('Select the timezone to which values will be converted.'),
      '#options' => $this->buildTimeZoneResolverOptions(),
      '#default_value' => $this->getSetting('timezone'),
    ];

    return $element;
  }

  /**
   * Build a renderable array of timezone resolver options.
   *
   * @return array
   *   An array of the form PLUGIN_ID => LABEL.
   */
  protected function buildTimeZoneResolverOptions() {
    return array_map(function (array $plugin_definition) {
      return $plugin_definition['label'];
    }, $this->dateTimeZoneResolverManager()->getDefinitions());
  }

}
