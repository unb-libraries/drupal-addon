<?php

namespace Drupal\datetime_plus\Plugin\TimeZoneResolver;

/**
 * Interface for timezone resolver plugins.
 *
 * @package Drupal\datetime_plus\Plugin\TimeZoneResolver
 */
interface DateTimeZoneResolverInterface {

  /**
   * Produce a timezone.
   *
   * @return \DateTimeZone
   *   A timezone object.
   */
  public function getTimeZone();

}
