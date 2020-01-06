<?php

namespace Drupal\testgen\Commands;

use Drush\Commands\DrushCommands;

class TestgenCommands extends DrushCommands {

  /**
   * Echos back hello with the argument provided.
   *
   * @command testgen:generate
   * @aliases tgen
   */
  public function generate() {
    $generator = \Drupal::service('testgen.generator');
    $generator->generate();
  }

}