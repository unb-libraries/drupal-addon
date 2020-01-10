<?php

namespace Drupal\testgen\Commands;

use Drupal\testgen\generate\ModuleTestGenerator;
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
   *
   * @param \Drupal\testgen\generate\ModuleTestGenerator $generator
   *   Module generator service.
   */
  public function __construct(ModuleTestGenerator $generator) {
    parent::__construct();
    $this->generator = $generator;
  }

  /**
   * Generates test files for each given module.
   *
   * @param array $module_names
   *   Name of the module for which to generate test cases.
   *
   * @command testgen:generate:module
   * @aliases tg-module,tgm
   */
  public function generate(array $module_names) {
    foreach ($module_names as $module_name) {
      $this->generator()->generateTests($module_name);
    }
  }

}