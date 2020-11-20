<?php

namespace Drupal\testgen\generate;

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
   * Name of the module.
   *
   * @var string
   */
  protected $moduleName;

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
   * Retrieve the name of the module.
   *
   * @return string
   *   A string.
   */
  protected function getModuleName() {
    return $this->moduleName;
  }

  /**
   * Generate test cases for the given module.
   * Create a ModuleTestGenerator instance.
   *
   * @param $module_name
   *   Name of the module for which to generate test cases.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Module handler service.
   * @param string $module_name
   *   Name of a module.
   */
  public function generateTests($module_name) {
    if ($module = $this->getModule($module_name)) {
      $this->generate(
        $this->getModuleModelRoot($module),
        $this->getModuleTestRoot($module));
    }
  public function __construct(ModuleHandlerInterface $module_handler, string $module_name) {
    $this->moduleHandler = $module_handler;
    $this->moduleName = $module_name;
    static::tozart()
      ->subjectDiscovery()
      ->addDirectory($this->getSubjectRoot());
  }

  /**
   * Load a module instance, if one exists under the given name.
   *
   * @return \Drupal\Core\Extension\Extension|null
   *   An Extension instance.
   */
  protected function getModule() {
    try {
      return $this->moduleHandler()->getModule($this->getModuleName());
    } catch (UnknownExtensionException $e) {
      return NULL;
    }
  }

  /**
   * Retrieve the path to the test root folder.
   *
   * @return string
   *   Absolute directory path.
   */
  protected function getModuleTestRoot() {
    return $this->getModule()->getPath() . '/tests';
  }

  /**
   * Retrieve the path to the folder which contains subject definitions.
   *
   * @return string
   *   Absolute directory path.
   */
  protected function getSubjectRoot() {
    return $this->getModuleTestRoot() . '/subjects';
  }

}