<?php

namespace Drupal\testgen\generate;

use TestGen\generate\TestGenerator;

/**
 * Drupal wrapper for PHP-TestGen TestGenerator.
 *
 * @package Drupal\testgen\generate
 */
abstract class DrupalTestGenerator {

  /**
   * Configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private $config;

  /**
   * Original php-testgen generator.
   *
   * @var \TestGen\generate\TestGenerator
   */
  private $generator;

  protected function config() {
    return $this->config;
  }

  /**
   * Retrieve a php-testgen generator instance.
   *
   * @return mixed|\TestGen\generate\TestGenerator
   *   A php-testgen generator instance.
   */
  private function generator() {
    return $this->generator;
  }

  /**
   * Create a new DrupalTestGenerator instance.
   *
   * @param \Drupal\Core\Config\ImmutableConfig $config
   *   Configuration.
   */
  public function __construct($config) {
    $this->config = $config;
    $this->generator = new TestGenerator();
  }

  /**
   * Call the original generator to generate test cases.
   *
   * @param string $output_root
   *   Path to the output folder in which to put generated files.
   * @param string $template_root
   *   Path to the folder which to scan for template files.
   */
  protected function generate($output_root = TestGenerator::OUTPUT_ROOT, $template_root = TestGenerator::TEMPLATE_ROOT) {
    $this->generator()->generate($output_root, $template_root);
  }

}