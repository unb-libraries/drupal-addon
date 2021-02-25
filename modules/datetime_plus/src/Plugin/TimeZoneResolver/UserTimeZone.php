<?php

namespace Drupal\datetime_plus\Plugin\TimeZoneResolver;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\datetime_plus\Annotation\DateTimeZoneResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the "UserTimeZone" timezone resolver plugin.
 *
 * @DateTimeZoneResolver(
 *   id = "user",
 *   label = @Translation("User timezone"),
 * )
 *
 * @package Drupal\datetime_plus\Plugin\TimeZoneResolver
 */
class UserTimeZone extends PluginBase implements DateTimeZoneResolverInterface, ContainerFactoryPluginInterface {

  /**
   * The currently logged-in user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Retrieve the currently logged-in user.
   *
   * @return \Drupal\Core\Session\AccountProxyInterface
   *   A user account object.
   */
  protected function currentUser() {
    return $this->currentUser;
  }

  /**
   * Create a new UserTimeZone plugin instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The currently logged-in user.
   */
  public function __construct(array $configuration, string $plugin_id, $plugin_definition, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('current_user'));
  }

  /**
   * {@inheritDoc}
   */
  public function getTimeZone() {
    $timezone_name = $this->currentUser()
      ->getTimeZone();
    return new \DateTimeZone($timezone_name);
  }

}
