<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * Trait to always enable entity validation.
 *
 * By default Drupal only validates entities that have been created or edited
 * through form submission.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
trait EntityValidationTrait {

  /**
   * {@inheritDoc}
   */
  public function onChange($name) {
    $this->setValidationRequired(TRUE);
    parent::onChange($name);
  }

  /**
   * {@inheritDoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    if ($this->isValidationRequired() && !$this->validated) {
      /** @var \Symfony\Component\Validator\ConstraintViolationListInterface $violations */
      $violations = $this->validate();
      if ($violations->count() > 0) {
        $message = 'Entity did not validate: ' . "\n";
        foreach ($violations->getFieldNames() as $field_name) {
          $message .= sprintf("%s: %s\n",
            $field_name, $violations->getByField($field_name)->get(0)->getMessage()
          );
        }
        throw new ValidatorException($message);
      }
    }
    parent::preSave($storage);
  }

  /**
   * {@inheritDoc}
   */
  public function isValidationRequired() {
    return $this->validationRequired || $this->isNew();
  }

}
