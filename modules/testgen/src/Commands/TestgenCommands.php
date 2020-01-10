<?php

namespace Drupal\testgen\Commands;

use Drush\Commands\DrushCommands;

class TestgenCommands extends DrushCommands {

  const LOCAL_TEST_ROOT = '/tests/behat/features/example';
  const GLOBAL_TEST_ROOT = '/app' . self::LOCAL_TEST_ROOT;

  /**
   * Test generator service.
   *
   * @var \TestGen\generate\TestGenerator
   */
  protected $generator;

  /**
   * Module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Retrieve the test generator service.
   *
   * @return \TestGen\generate\TestGenerator
   *   A test generator service instance.
   */
  public function generator() {
    return $this->generator;
  }

  /**
   * Retrieves the module handler service.
   *
   * @return \Drupal\Core\Extension\ModuleHandlerInterface
   *   A module handler service instance.
   */
  public function moduleHandler() {
    return $this->moduleHandler;
  }

  /**
   * Creates the TestgenCommands instance.
   */
  public function __construct() {
    $this->generator = \Drupal::service('testgen.generator');
    $this->moduleHandler = \Drupal::moduleHandler();
  }

  /**
   * Generates test files in the GLOBAL_TEST_ROOT folder.
   *
   * @param array $module_names
   *   Name of the module for which to generate test cases.
   *
   * @command testgen:generate
   * @aliases tgen
   */
  public function generate(array $module_names) {
    if (!empty($module_names)) {
      foreach ($module_names as $module_name) {
        $module = $this->moduleHandler()->getModule($module_name);
        $module_test_path = $module->getPath() . self::LOCAL_TEST_ROOT;
        $this->generator()->generate($module_test_path);
      }
    }
    else {
      $this->generator()->generate(self::GLOBAL_TEST_ROOT);
    }
  }

}