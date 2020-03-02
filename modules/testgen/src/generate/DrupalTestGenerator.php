<?php

namespace Drupal\testgen\generate;

use TestGen\TestGen;

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
   * Inject the actual generator.
   *
   * @param \TestGen\TestGen $testgen
   *   A TestGen service instance.
   */
  public function setGenerator(TestGen $testgen) {
    $this->generator = $testgen->generator();
  }

  /**
   * Create a new DrupalTestGenerator instance.
   *
   * @param \Drupal\Core\Config\ImmutableConfig $config
   *   Configuration.
   */
  public function __construct($config) {
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
    $this->generator()->setModelRoot($model_root);
    $this->generator()->setOutputRoot($output_root);
    $this->generator()->generate();
  }

}
