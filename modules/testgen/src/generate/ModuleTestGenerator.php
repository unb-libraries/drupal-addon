<?php

namespace Drupal\testgen\generate;

use Drupal\Core\Config\ImmutableConfig;
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
   * @param \Drupal\Core\Config\ImmutableConfig $config
   *   Configuration.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Module handler service.
   */
  public function __construct(ImmutableConfig $config, ModuleHandlerInterface $module_handler) {
    parent::__construct($config);
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
      $this->generate($this->getModuleTestRoot($module));
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

  /**
   * Retrieve the path to where the given module keeps its tests.
   *
   * @param \Drupal\Core\Extension\Extension $module
   *   The module instance.
   *
   * @return string
   *   Absolute path to the given module's test root.
   */
  protected function getModuleTestRoot(Extension $module) {
    return $module->getPath() . $this->config()->get(self::TEST_ROOT);
  }

}