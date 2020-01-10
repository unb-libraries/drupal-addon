<?php

namespace Drupal\testgen\generate;

class SiteTestGenerator extends DrupalTestGenerator {

  protected const TEST_ROOT = '/app/tests/behat/features/example';

  /**
   * Generate test cases that apply to an entire site.
   */
  public function generateTests() {
    $this->generate(self::TEST_ROOT);
  }

}
