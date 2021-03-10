<?php

namespace Drupal\datetime_plus\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\datetime_plus\Datetime\TimezoneAwareDateTimeComputed;

/**
 * Plugin implementation of the 'datetime_plus' field type.
 *
 * @FieldType(
 *   id = "datetime_timezone",
 *   label = @Translation("Date (custom timezone)"),
 *   description = @Translation("Create and store timezone-customizable date values."),
 *   default_widget = "datetime_default",
 *   default_formatter = "datetime_default",
 *   list_class = "\Drupal\datetime\Plugin\Field\FieldType\DateTimeFieldItemList",
 *   constraints = {"DateTimeFormat" = {}}
 * )
 */
class TimezoneAwareDateTimeItem extends DateTimeItem {

  /**
   * The timezone resolver service.
   *
   * @var \Drupal\datetime_plus\Plugin\TimeZoneResolver\DateTimeZoneResolverManagerInterface
   */
  protected static $dateTimeZoneResolverManager;

  /**
   * Get the timezone resolver service.
   *
   * @return \Drupal\datetime_plus\Plugin\TimeZoneResolver\DateTimeZoneResolverManagerInterface
   *   A timezone resolver plugin manager.
   */
  protected static function dateTimeZoneResolverManager() {
    return static::$dateTimeZoneResolverManager;
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(DataDefinitionInterface $definition, $name = NULL, TypedDataInterface $parent = NULL) {
    // @todo Replace by proper dependency injection once FieldType plugins support it.
    self::$dateTimeZoneResolverManager = \Drupal::service('plugin.manager.timezone_resolver');
    parent::__construct($definition, $name, $parent);
  }

  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $resolver = static::dateTimeZoneResolverManager()
      ->createInstance($field_definition
      ->getSetting('timezone'));
    $properties['date']
      ->setClass(TimezoneAwareDateTimeComputed::class)
      ->setSetting('timezone', $resolver->getTimeZone());

    return $properties;
  }

  /**
   * {@inheritDoc}
   */
  public static function defaultFieldSettings() {
    return [
      'timezone' => 'user',
    ] + parent::defaultFieldSettings();
  }

}
