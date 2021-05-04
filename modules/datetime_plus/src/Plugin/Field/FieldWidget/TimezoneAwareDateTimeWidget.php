<?php

namespace Drupal\datetime_plus\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\datetime\Plugin\Field\FieldWidget\DateTimeDefaultWidget;
use Drupal\datetime_plus\Datetime\DrupalDateTimePlus;
use Drupal\datetime_plus\Plugin\TimeZoneResolver\DateTimeZoneResolverManagerInterface;
use Drupal\datetime_plus\Plugin\TimeZoneResolver\DateTimeZoneResolverTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Datetime widget that uses the timezone configured by the according field.
 *
 * @FieldWidget(
 *   id = "datetime_timezone",
 *   label = @Translation("Date and time (custom timezone)"),
 *   field_types = {
 *     "datetime_timezone"
 *   }
 * )
 *
 * @package Drupal\datetime_plus\Plugin\Field\FieldWidget
 */
class TimezoneAwareDateTimeWidget extends DateTimeDefaultWidget {

  use DateTimeZoneResolverTrait;

  /**
   * TimezoneAwareDateTimeWidget constructor.
   *
   * {@inheritDoc}
   *
   * @param \Drupal\datetime_plus\Plugin\TimeZoneResolver\DateTimeZoneResolverManagerInterface $date_time_zone_resolver_manager
   *   A timezone resolver plugin manager.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, EntityStorageInterface $date_storage, DateTimeZoneResolverManagerInterface $date_time_zone_resolver_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings, $date_storage);
    static::$dateTimeZoneResolverManager = $date_time_zone_resolver_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager')->getStorage('date_format'),
      $container->get('plugin.manager.timezone_resolver')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $timezone = static::resolve(
      $this->getFieldSetting('timezone'),
      $this->getFieldSetting('resolver_settings')
    );
    $element['value']['#date_timezone'] = $timezone->getName();

    if (!$items[$delta]->date) {
      $default_value = $items[$delta]->getFieldDefinition()->getDefaultValueLiteral();
      if (!empty($default_value) && isset($default_value[$delta])) {
        $date = new DrupalDateTimePlus($default_value[$delta]['value'], $timezone->getName());
        $element['value']['#default_value'] = $this->createDefaultValue($date, $element['value']['#date_timezone']);
      }
    }
    else {
      $date = $items[$delta]->date;
      // The date was created and verified during field_load(), so it is safe to
      // use without further inspection.
      $date->setTimezone(new \DateTimeZone($element['value']['#date_timezone']));
      $element['value']['#default_value'] = $this->createDefaultValue($date, $element['value']['#date_timezone']);
    }

    return $element;
  }

}
