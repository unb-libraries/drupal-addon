<?php

namespace Drupal\lib_unb_custom_entity\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Entity\ContentEntityConfirmFormBase;

/**
 * Form to confirm the termination of a content entity.
 *
 * @package Drupal\lib_unb_custom_entity\Form
 */
class ContentEntityTerminateForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritDoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to terminate the @entity_type @label?', [
      '@entity_type' => $this->getEntity()->getEntityType()->getSingularLabel(),
      '@label' => $this->getEntity()->label()
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('This will disable future editing of the entity.');
  }

  /**
   * {@inheritDoc}
   */
  public function getCancelUrl() {
    $entity_type_id = $this->getEntity()->getEntityTypeId();
    return new Url("entity.{$entity_type_id}.collection");
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Terminate');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\lib_unb_custom_entity\Entity\TerminableInterface $terminable_entity */
    $terminable_entity = $this->getEntity();
    if (!$terminable_entity->isTerminated()) {
      $terminable_entity->terminate();
      $this->messenger()->addStatus($this->t('@entity_type "@label" terminated.', [
        '@entity_type' => ucfirst($this->getEntity()->getEntityType()->getSingularLabel()),
        '@label' => $this->entity->label(),
      ]));
    }
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
