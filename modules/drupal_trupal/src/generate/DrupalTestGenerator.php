<?php

namespace Drupal\drupal_trupal\generate;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\drupal_trupal\Trupal\TrupalTrait;

/**
 * Drupal wrapper for the Trupal generator.
 *
 * @package Drupal\drupal_trupal\generate
 */
abstract class DrupalTestGenerator {

  use TrupalTrait;

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
   * @param string $subject_root
   *   Path to the folder which to scan for model files.
   * @param string $output_root
   *   Path to the output folder in which to put generated files.
   */
  protected function generate($subject_root, $output_root) {
    static::trupal()->generate($subject_root, $output_root);
  }

}
