<?php

namespace Drupal\datetime_plus\Datetime;

interface DateTimeFactoryInterface {

  /**
   * Retrieve the timezone which is applied to all created datetime objects.
   *
   * @return \DateTimeZone
   *   A timezone object or name.
   */
  public function getTimeZone();

  /**
   * Set the timezone which is applied to all created datetime objects.
   *
   * @param \DateTimeZone|string $timezone
   *   A timezone object or name.
   */
  public function setTimeZone($timezone);

  /**
   * Creates a date object from timestamp input.
   *
   * The created datetime object will be converted
   * to the timezone of the factory.
   *
   * @param int $timestamp
   *   A UNIX timestamp.
   * @param array $settings
   *   @see \Drupal\datetime_plus\Datetime\DrupalDateTimePlus::__construct()
   *
   * @return \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   *   A datetime object.
   *
   * @throws \InvalidArgumentException
   *   If the timestamp is not numeric.
   */
  public function createFromTimestamp($timestamp, $settings = []);

  /**
   * Creates a date object from an array of date parts.
   *
   * Converts the input value into an ISO date, forcing a full ISO
   * date even if some values are missing.
   *
   * @param array $date_parts
   *   An array of date parts, like ('year' => 2014, 'month' => 4).
   * @param array $settings
   *   (optional) A keyed array for settings.
   *
   * @see \Drupal\datetime_plus\Datetime\DrupalDateTimePlus::__construct()
   *
   * @return \Drupal\datetime_plus\Datetime\DrupalDateTimePlus
   *   A datetime object.
   *
   * @throws \InvalidArgumentException
   *   If the array date values or value combination is not correct.
   */
  public function createFromArray(array $date_parts, $settings = []);

  /**
   * Creates a date object from an input format.
   *
   * @param string $format
   *   PHP date() type format for parsing the input. This is recommended
   *   to use things like negative years, which php's parser fails on, or
   *   any other specialized input with a known format. If provided the
   *   date will be created using the createFromFormat() method.
   *   @see http://php.net/manual/datetime.createfromformat.php
   * @param mixed $time
   *   @see \Drupal\datetime_plus\Datetime\DrupalDateTimePlus::__construct()
   * @param array $settings
   *   - validate_format: (optional) Boolean choice to validate the
   *     created date using the input format. The format used in
   *     createFromFormat() allows slightly different values than format().
   *     Using an input format that works in both functions makes it
   *     possible to a validation step to confirm that the date created
   *     from a format string exactly matches the input. This option
   *     indicates the format can be used for validation. Defaults to TRUE.
   *   @see __construct()
   *
   * @return static
   *   A new DateTimePlus object.
   *
   * @throws \InvalidArgumentException
   *   If the a date cannot be created from the given format.
   * @throws \UnexpectedValueException
   *   If the created date does not match the input value.
   */
  public function createFromFormat($format, $time, $settings = []);

}