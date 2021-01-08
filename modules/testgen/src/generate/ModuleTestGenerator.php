<?php

namespace Drupal\testgen\generate;

use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\Exception\UnknownExtensionException;

/**
 * Generates test cases for modules.
 *
 * @package Drupal\testgen\generate
 */
class ModuleTestGenerator extends DrupalTestGenerator {

  protected const TEST_ROOT = 'module_root';

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
    $this->moduleHandler = $module_handler;
  }

  /**
   * Generate test cases for the given module.
   *
   * @param $module_name
   *   Name of the module for which to generate test cases.
   */
  public function generateTests($module_name) {
    if ($module = $this->getModule($module_name)) {
      $this->generate(
        $this->getSubjectRoot($module),
        $this->getModuleTestRoot($module));
    }
  }

  /**
   * Load a module instance, if one exists under the given name.
   *
   * @param string $module_name
   *
   * @return \Drupal\Core\Extension\Extension|null
   *   An Extension instance.
   */
  protected function getModule(string $module_name) {
    try {
      return $this->moduleHandler()->getModule($module_name);
    }
    catch (UnknownExtensionException $e) {
      return NULL;
    }
  }

  /**
   * Retrieve the path to the test root folder.
   *
   * @param \Drupal\Core\Extension\Extension $module
   *   A Drupal module.
   *
   * @return string
   *   Absolute directory path.
   */
  protected function getModuleTestRoot(Extension $module) {
    return $module->getPath() . '/tests';
  }

  /**
   * Retrieve the path to the folder which contains subject definitions.
   *
   * @param \Drupal\Core\Extension\Extension $module
   *   A Drupal module.
   *
   * @return string
   *   Absolute directory path.
   */
  protected function getSubjectRoot(Extension $module) {
    return $this->getModuleTestRoot($module) . '/subjects';
  }

}
