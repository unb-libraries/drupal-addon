<?php

namespace Drupal\lib_unb_custom_entity\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that a field is assigned a unique value.
 *
 * @Constraint(
 *   id = "UniqueValue",
 *   label = @Translation("Unique value", context = "Validation"),
 * )
 *
 * @package Drupal\lib_unb_custom_entity\Plugin\Validation\Constraint
 */
class UniqueValue extends Constraint {

  /**
   * {@inheritDoc}
   */
  public function getTargets() {
    return self::PROPERTY_CONSTRAINT;
  }

}
