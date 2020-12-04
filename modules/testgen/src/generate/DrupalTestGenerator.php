<?php

namespace Drupal\testgen\generate;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\testgen\Tozart\TozartTrait;

/**
 * Drupal wrapper for PHP-TestGen TestGenerator.
 *
 * @package Drupal\testgen\generate
 */
abstract class DrupalTestGenerator {

  use TozartTrait;

  /**
   * Configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private $config;

  /**
   * Retrieve the configuration for the generator.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   An immutable configuration object.
   */
  public function config() {
    return $this->config;
  }

  /**
   * Assign configuration to this generator.
   *
   * @param \Drupal\Core\Config\ImmutableConfig $config
   *   A configuration object.
   */
  public function setConfig(ImmutableConfig $config) {
    $this->config = $config;
  }

  /**
   * Call the original generator to generate test cases.
   *
   * @param string $model_root
   *   Path to the folder which to scan for model files.
   * @param string $output_root
   *   Path to the output folder in which to put generated files.
   */
  protected function generate($model_root, $output_root) {
    static::tozart()->generate($output_root);
  }

}
