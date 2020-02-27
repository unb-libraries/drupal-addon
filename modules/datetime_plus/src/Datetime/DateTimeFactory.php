<?php

namespace Drupal\datetime_plus\Datetime;

/**
 * Factory to create datetime objects.
 *
 * @package Drupal\datetime_plus\Datetime
 */
class DateTimeFactory implements DateTimeFactoryInterface {

  /**
   * The timezone.
   *
   * @var \DateTimeZone
   */
  protected $timeZone;

  /**
   * The timezone resolver.
   *
   * @var \Drupal\datetime_plus\Datetime\DateTimeZoneResolverInterface
   */
  protected $timeZoneResolver;

  /**
   * {@inheritDoc}
   */
  public function getTimeZone() {
    return $this->timeZone;
  }

  /**
   * {@inheritDoc}
   */
  public function setTimeZone($timezone) {
    if (is_string($timezone)) {
      $timezone = $this->createTimeZone($timezone);
    }
    $this->timeZone = $timezone;
  }

  /**
   * Retrieve the timezone resolver service.
   *
   * @return \Drupal\datetime_plus\Datetime\DateTimeZoneResolverInterface
   *   A timezone resolver.
   */
  protected function getTimeZoneResolver() {
    return $this->timeZoneResolver;
  }

  /**
   * Create a new datetime factory instance.
   *
   * @param \Drupal\datetime_plus\Datetime\DateTimeZoneResolverInterface $timezone_resolver
   *   A timezone resolver.
   */
  public function __construct(DateTimeZoneResolverInterface $timezone_resolver) {
    $this->timeZone = date_default_timezone_get();
    $this->timeZoneResolver = $timezone_resolver;
  }

  /**
   * Create a timezone object from the given timezone name.
   *
   * @param string $timezone_name
   *   The timezone name. Either a "dynamic" timezone or a
   *   supported PHP timezone.
   *
   * @see \Drupal\datetime_plus\Datetime\DateTimeZoneResolverInterface
   * @link https://www.php.net/manual/en/timezones.php
   *
   * @return \DateTimeZone
   *   A timezone object.
   */
  public function createTimeZone($timezone_name) {
    $resolved_timezone_name = $this
      ->getTimeZoneResolver()
      ->resolveTimeZone($timezone_name);
    return new \DateTimeZone($resolved_timezone_name);
  }

  /**
   * {@inheritDoc}
   */
  public function createFromTimestamp($timestamp, $settings = []) {
    $datetime = DrupalDateTimePlus::createFromTimestamp(
      $timestamp, NULL, $settings);
    $datetime->setTimezone($this->getTimeZone());
    return $datetime;
  }

  /**
   * {@inheritDoc}
   */
  public function createFromArray(array $date_parts, $settings = []) {
    return DrupalDateTimePlus::createFromArray(
      $date_parts, $this->getTimeZone(), $settings);
  }

  /**
   * {@inheritDoc}
   */
  public function createFromFormat($format, $time, $settings = []) {
    return DrupalDateTimePlus::createFromFormat(
      $format, $time, $this->getTimeZone(), $settings);
  }

}
