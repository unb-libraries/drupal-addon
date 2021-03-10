<?php

namespace Drupal\datetime_plus\Plugin\TimeZoneResolver;

use Drupal\Component\Plugin\Exception\PluginException;

/**
 * Provides methods to resolve (dynamic) timezone names.
 *
 * @package Drupal\datetime_plus\Plugin\TimeZoneResolver
 */
trait DateTimeZoneResolverTrait {

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
   * Resolve the given timezone name, to a timezone object.
   *
   * @param string $timezone_name
   *   A (dynamic) timezone name, e.g. "user".
   *
   * @return \DateTimeZone
   *   A timezone object.
   */
  protected static function resolve($timezone_name) {
    try {
      return static::dateTimeZoneResolverManager()
        ->createInstance($timezone_name)
        ->getTimeZone();
    }
    catch (PluginException $e) {
      // @todo Log the error.
      return new \DateTimeZone('UTC');
    }
  }

}
