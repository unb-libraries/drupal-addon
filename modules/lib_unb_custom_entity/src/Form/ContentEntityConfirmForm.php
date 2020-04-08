<?php

namespace Drupal\lib_unb_custom_entity\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Enhanced implementation of a generic base class for an entity-based confirmation form.
 *
 * @package Drupal\lib_unb_custom_entity\Form
 */
class ContentEntityConfirmForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the @entity_type @label?', [
      '@entity_type' => $this->getEntity()->getEntityType()->getSingularLabel(),
      '@label' => $this->getEntity()->label()
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    $entity_type_id = $this->getEntity()->getEntityTypeId();
    return new Url("entity.{$entity_type_id}.collection");
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->getEntity()->delete();
    $this->messenger()->addStatus($this->t('@entity_type "@label" deleted.', [
      '@entity_type' => ucfirst($this->getEntity()->getEntityType()->getSingularLabel()),
      '@label' => $this->entity->label(),
    ]));

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
