<?php

namespace Drupal\datetime_plus\Datetime;

use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Resolves dynamic to actual timezone names.
 *
 * @package Drupal\datetime_plus\Datetime
 */
class DateTimeZoneResolver implements DateTimeZoneResolverInterface {

  /**
   * {@inheritDoc}
   */
  public function resolveTimeZone($timezone_name) {
    $resolver = [$this, "resolve{$timezone_name}TimeZone"];
    if (is_callable($resolver)) {
      return call_user_func($resolver);
    }
    return $this->resolveCustomTimeZone($timezone_name);
  }

  /**
   * Resolve to the currently logged-in user's timezone name.
   *
   * @return string
   *   A timezone name.
   */
  protected function resolveUserTimeZone() {
    if (!empty($user_timezone = $this->currentUser()->getTimeZone())) {
      return $this->resolveCustomTimeZone($user_timezone);
    }
    return $this->resolveSystemTimeZone();
  }

  /**
   * Retrieve the currently logged-in user.
   *
   * @return \Drupal\Core\Session\AccountProxyInterface
   *   A user account object.
   */
  protected function currentUser() {
    return \Drupal::currentUser();
  }

  /**
   * Resolve to the system's timezone name.
   *
   * @return string
   *   A timezone name.
   */
  protected function resolveSystemTimeZone() {
    $system_timezone = \Drupal::config('system.date')
      ->get('timezone.default');
    return $this->resolveCustomTimeZone($system_timezone);
  }

  /**
   * Resolve to the name of the timezone which is used to store datetime values.
   *
   * @return string
   *   A timezone name.
   */
  protected function resolveStorageTimeZone() {
    return DateTimeItemInterface::STORAGE_TIMEZONE;
  }

  /**
   * Resolve a custom timezone name.
   *
   * @param $timezone_name
   *   The name of the timezone to which to resolve.
   *
   * @return string
   *   A timezone name.
   */
  protected function resolveCustomTimeZone($timezone_name) {
    if (in_array($timezone_name, timezone_identifiers_list())) {
      return $timezone_name;
    }
    return date_default_timezone_get();
  }

}
