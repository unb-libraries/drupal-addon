<?php

namespace Drupal\datetime_plus\Plugin\TimeZoneResolver;

use Drupal\Core\Form\FormStateInterface;

/**
 * Resolves to a static timezone.
 *
 * @DateTimeZoneResolver(
 *   id = "static",
 *   label = @Translation("Static timezone"),
 * )
 *
 * @package Drupal\datetime_plus\Plugin\TimeZoneResolver
 */
class StaticTimeZone extends DateTimeZoneResolverBase {

  const FALLBACK_TIMEZONE = 'UTC';

  /**
   * {@inheritDoc}
   */
  public function getTimeZone() {
    return $this->createFromName($this->configuration['name']);
  }

  /**
   * Create a timezone object from a given timezone name.
   *
   * @param string $timezone_name
   *   A timezone name, e.g. "America/Moncton".
   *
   * @return \DateTimeZone
   *   A timezone object.
   */
  protected function createFromName(string $timezone_name) {
    try {
      return new \DateTimeZone($timezone_name);
    }
    catch (\Exception $e) {
      return new \DateTimeZone(self::FALLBACK_TIMEZONE);
    }
  }

  /**
   * {@inheritDoc}
   */
  protected function defaultSettings() {
    return [
      'name' => self::FALLBACK_TIMEZONE,
    ] + parent::defaultSettings();
  }

}
