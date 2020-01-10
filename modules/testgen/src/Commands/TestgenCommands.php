<?php

namespace Drupal\testgen\Commands;

use Drush\Commands\DrushCommands;

class TestgenCommands extends DrushCommands {

  const GLOBAL_TEST_ROOT = '/app/tests/behat/features/example/';

  /**
   * Test generator service.
   *
   * @var \TestGen\generate\TestGenerator
   */
  protected $generator;

  /**
   * Retrieve the test generator service.
   *
   * @return \TestGen\generate\TestGenerator
   *   A test generator service instance.
   */
  public function generator() {
    if (!isset($this->generator)) {
      $this->generator = \Drupal::service('testgen.generator');
    }
    return $this->generator;
  }

  /**
   * Generates test files in the GLOBAL_TEST_ROOT folder.
   *
   * @command testgen:generate
   * @aliases tgen
   */
  public function generate() {
    $this->generator()->generate(self::GLOBAL_TEST_ROOT);
  }

}