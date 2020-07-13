<?php

namespace Drupal\lib_unb_custom_entity\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Entity\ContentEntityConfirmFormBase;

/**
 * Form to confirm the restoration of a content entity.
 *
 * @package Drupal\lib_unb_custom_entity\Form
 */
class ContentEntityRestoreForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritDoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to restore the @entity_type @label?', [
      '@entity_type' => $this->getEntity()->getEntityType()->getSingularLabel(),
      '@label' => $this->getEntity()->label()
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('This will re-enable editing of the entity.');
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
    return $this->t('Restore');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\lib_unb_custom_entity\Entity\TerminableInterface $restorable_entity */
    $restorable_entity = $this->getEntity();
    if ($restorable_entity->isTerminated()) {
      $restorable_entity->restore();
      $this->messenger()->addStatus($this->t('@entity_type "@label" restored.', [
        '@entity_type' => ucfirst($this->getEntity()->getEntityType()->getSingularLabel()),
        '@label' => $this->entity->label(),
      ]));
    }

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}