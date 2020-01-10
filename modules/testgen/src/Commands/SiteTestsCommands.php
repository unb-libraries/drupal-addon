<?php

namespace Drupal\testgen\Commands;

use Drupal\testgen\generate\SiteTestGenerator;
use Drush\Commands\DrushCommands;

class SiteTestsCommands extends DrushCommands {

  /**
   * Test generator service.
   *
   * @var \Drupal\testgen\generate\SiteTestGenerator
   */
  protected $generator;

  /**
   * Retrieve the test generator service.
   *
   * @return \Drupal\testgen\generate\SiteTestGenerator
   *   A test generator service instance.
   */
  public function generator() {
    return $this->generator;
  }

  /**
   * Creates the SiteTestsCommands instance.
   *
   * @param \Drupal\testgen\generate\SiteTestGenerator $generator
   *   Module generator service.
   */
  public function __construct(SiteTestGenerator $generator) {
    parent::__construct();
    $this->generator = $generator;
  }

  /**
   * Generates test files that apply to an entire site.
   *
   * @command testgen:generate:site
   * @aliases tg-site,tgs
   */
  public function generate() {
    $this->generator()->generateTests();
  }

}