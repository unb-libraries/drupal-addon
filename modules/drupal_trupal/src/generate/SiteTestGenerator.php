<?php

namespace Drupal\drupal_trupal\generate;

/**
 * Generates test cases which apply to an entire site.
 *
 * @package Drupal\drupal_trupal\generate
 */
class SiteTestGenerator extends DrupalTestGenerator {

  protected const TEST_ROOT = 'site_root';

  /**
   * Generate test cases that apply to an entire site.
   */
  public function generateTests() {
    $this->generate($this->getSiteTestRoot());
  }

  /**
   * Retrieve the path to where the site keeps its global tests.
   *
   * @return string
   *   Absolute path to the site's test root.
   */
  protected function getSiteTestRoot() {
    return $this->config()->get(self::TEST_ROOT);
  }

}
