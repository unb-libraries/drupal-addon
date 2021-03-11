<?php

namespace Drupal\datetime_plus\Plugin\TimeZoneResolver;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;

/**
 * Base class for DateTimeZoneResolver plugins.
 *
 * @package Drupal\datetime_plus\Plugin\TimeZoneResolver
 */
abstract class DateTimeZoneResolverBase extends PluginBase implements DateTimeZoneResolverInterface {

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    // Remove irrelevant configuration, fill with default configuration.
    $default_configuration = $this->defaultSettings();
    $configuration = array_intersect_key($configuration, $default_configuration);
    $configuration += $default_configuration;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * Get the default plugin configuration.
   *
   * @return array
   *   An array. Refer to subclasses for accepted keys and values.
   */
  protected function defaultSettings() {
    return [];
  }

}
