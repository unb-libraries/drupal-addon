<?php

namespace Drupal\testgen\generate;

use TestGen\generate\TestGenerator;

abstract class DrupalTestGenerator {

  /**
   * Original php-testgen generator.
   *
   * @var \TestGen\generate\TestGenerator
   */
  private $generator;

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
   */
  public function __construct() {
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