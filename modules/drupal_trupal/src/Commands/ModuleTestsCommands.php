<?php

namespace Drupal\drupal_trupal\Commands;

use Drupal\drupal_trupal\generate\ModuleTestGenerator;
use Drush\Commands\DrushCommands;

class ModuleTestsCommands extends DrushCommands {

  /**
   * Test generator service.
   *
   * @var \Drupal\drupal_trupal\generate\ModuleTestGenerator
   */
  protected $generator;

  /**
   * Retrieve the test generator service.
   *
   * @return \Drupal\drupal_trupal\generate\ModuleTestGenerator
   *   A test generator service instance.
   */
  public function generator() {
    return $this->generator;
  }

  /**
   * Creates the ModuleTestsCommands instance.
   *
   * @param \Drupal\drupal_trupal\generate\ModuleTestGenerator $generator
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
   * @param array $options
   *  Additional, optional parameters.
   *
   * @command trupal:generate:module
   *
   * @option source-dir
   * @option output-dir
   *
   * @aliases tg-module,tgm
   */
  public function generate(array $module_names, array $options = ['source-dir' => '', 'output-dir' => '']) {
    $params = [];
    if ($options['source-dir']) {
      $params['trupalDir'] = $options['source-dir'];
    }
    if ($options['output-dir']) {
      $params['outputDir'] = $options['output-dir'];
    }
    foreach ($module_names as $module_name) {
      $this->generator()->generateTests($module_name, $params);
    }
  }

}
