<?php

namespace Drupal\testgen\generate;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\Exception\UnknownExtensionException;

class ModuleTestGenerator extends DrupalTestGenerator {

  protected const TEST_ROOT = '/tests/behat/features/example';

  /**
   * Module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

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
   * Create a ModuleTestGenerator instance.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Module handler service.
   */
  public function __construct(ModuleHandlerInterface $module_handler) {
    parent::__construct();
    $this->moduleHandler = $module_handler;
  }

  /**
   * Generate test cases for the given module.
   *
   * @param $module_name
   *   Name of the module for which to generate test cases.
   */
  public function generateModuleTests($module_name) {
    if ($module = $this->getModule($module_name)) {
      $module_test_path = $module->getPath() . self::TEST_ROOT;
      $this->generate($module_test_path);
    }
  }

  /**
   * Load a module instance, if one exists under the given name.
   *
   * @param $module_name
   *   The name of the module to load.
   *
   * @return \Drupal\Core\Extension\Extension|null
   *   An Extension instance.
   */
  protected function getModule($module_name) {
    try {
      return $this->moduleHandler()->getModule($module_name);
    } catch (UnknownExtensionException $e) {
      return NULL;
    }
  }

}