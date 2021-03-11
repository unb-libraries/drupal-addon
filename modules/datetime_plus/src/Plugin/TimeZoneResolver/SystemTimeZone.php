<?php

namespace Drupal\datetime_plus\Plugin\TimeZoneResolver;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Resolves to the system configured timezone.
 *
 * @DateTimeZoneResolver(
 *   id = "system",
 *   label = @Translation("System timezone"),
 * )
 *
 * @package Drupal\datetime_plus\Plugin\TimeZoneResolver
 */
class SystemTimeZone extends DateTimeZoneResolverBase implements ContainerFactoryPluginInterface {

  /**
   * The configuration containing system timezone settings.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $systemDateConfig;

  /**
   * Get the configuration containing system timezone settings.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   An immutable config object.
   */
  protected function getSystemDateConfig() {
    return $this->systemDateConfig;
  }

  /**
   * Constructs a SystemTimeZone plugin.
   *
   * @param \Drupal\Core\Config\ImmutableConfig $system_date_config
   *   Configuration containing the system timezone setting.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(ImmutableConfig $system_date_config, array $configuration, string $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->systemDateConfig = $system_date_config;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $system_date_config = $container->get('config.factory')
      ->get('system.date');
    return new static($system_date_config, $configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritDoc}
   */
  public function getTimeZone() {
    $timezone_name = $this->getSystemDateConfig()
      ->get('timezone')['default'];
    return new \DateTimeZone($timezone_name);
  }

}
