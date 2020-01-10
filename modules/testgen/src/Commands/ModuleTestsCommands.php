<?php

namespace Drupal\testgen\Commands;

use Drush\Commands\DrushCommands;

class ModuleTestsCommands extends DrushCommands {

  /**
   * Test generator service.
   *
   * @var \Drupal\testgen\generate\ModuleTestGenerator
   */
  protected $generator;

  /**
   * Retrieve the test generator service.
   *
   * @return \Drupal\testgen\generate\ModuleTestGenerator
   *   A test generator service instance.
   */
  public function generator() {
    return $this->generator;
  }

  /**
   * Creates the TestgenCommands instance.
   */
  public function __construct() {
    parent::__construct();
    $this->generator = \Drupal::service('test_generator.module');
  }

  /**
   * Generates test files in the GLOBAL_TEST_ROOT folder.
   *
   * @param array $module_names
   *   Name of the module for which to generate test cases.
   *
   * @command testgen:generate:module
   * @aliases tg-module,tgm
   */
  public function generate(array $module_names) {
    foreach ($module_names as $module_name) {
      $this->generator()->generateModuleTests($module_name);
    }
  }

}