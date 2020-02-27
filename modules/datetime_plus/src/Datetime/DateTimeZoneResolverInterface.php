<?php

namespace Drupal\datetime_plus\Datetime;

interface DateTimeZoneResolverInterface {

  const USER = 'user';
  const SYSTEM = 'system';
  const STORAGE = 'storage';

  /**
   * Resolves the dynamic timezone name into an actual timezone name.
   *
   * @param string $timezone_name
   *   The timezone. Accepts an actual PHP supported timezone
   *   name or one of the following:
   *   - user: resolves to the currently logged-in user's timezone.
   *   - system: resolves to the system's timezone.
   *   - storage: resolves to the timezone that is used at database level.
   *
   * @return string
   *   An actual timezone name.
   */
  public function resolveTimeZone($timezone_name);

}