<?php

namespace Drupal\lib_unb_custom_entity\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to confirm the deletion of all entities.
 *
 * @package Drupal\lib_unb_custom_entity\Form
 */
class EntityDeleteAllForm extends ConfirmFormBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface
   */
  protected $entityType;

  /**
   * The entity type ID.
   *
   * @var string
   */
  protected $entityTypeId;

  /**
   * Retrieve the entity type manager service.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   An entity type manager object.
   */
  protected function getEntityTypeManager() {
    return $this->entityTypeManager;
  }

  /**
   * Retrieve the entity type ID.
   *
   * @return string
   *   A string.
   */
  protected function getEntityTypeId() {
    if (!isset($this->entityTypeId)) {
      $this->entityTypeId = $this->getRouteMatch()->getParameter('entity_type_id');
    }
    return $this->entityTypeId;
  }

  /**
   * Retrieve the entity type.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   An entity type object.
   */
  protected function getEntityType() {
    if (!isset($this->entityType)) {
      $this->entityType = $this
        ->getStorage()
        ->getEntityType();
    }
    return $this->entityType;
  }

  /**
   * Create a new EntityDeleteAllForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager */
    $entity_type_manager = $container->get('entity_type.manager');
    return new static($entity_type_manager);
  }

  /**
   * Retrieve the number of entities.
   *
   * @return int
   *   An integer >= 0.
   */
  protected function getEntityCount() {
    $query = $this->getStorage()
      ->getQuery()
      ->count();
    return $query->execute();
  }

  /**
   * Retrieve the entities to be deleted.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entities.
   */
  protected function getEntities() {
    return $this->getStorage()
      ->loadMultiple();
  }

  /**
   * Retrieve an appropriate storage handler for the entity type.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   A storage handler instance.
   */
  protected function getStorage() {
    try {
      return $this->getEntityTypeManager()
        ->getStorage($this->getEntityTypeId());
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getQuestion() {
    $entity_count = $this->getEntityCount();
    return $this->formatPlural($entity_count,
      'Are you sure you want to delete the only @singular_entity_type',
      'Are you sure you want to delete all @count @plural_entity_type', [
        '@count' => $entity_count,
        '@singular_entity_type' => $this->getEntityType()->getSingularLabel(),
        '@plural_entity_type' => $this->getEntityType()->getPluralLabel(),
      ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritDoc}
   */
  public function getCancelUrl() {
    if ($this->entityType->hasLinkTemplate('collection')) {
      return new Url('entity.' . $this->getEntityTypeId() . '.collection');
    }
    else {
      return new Url('<front>');
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return $this->getEntityTypeId() . '_delete_all_confirm_form';
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $count_deleted = 0;
    foreach ($this->getEntities() as $entity) {
      if ($this->tryDelete($entity)) {
        $count_deleted++;
      }
    }
    $this->messenger()->addMessage($this->getDeletedMessage($count_deleted));
    $form_state->setRedirectUrl(Url::fromRoute('entity.' . $this->getEntityTypeId() . '.collection'));
  }

  /**
   * Try to permantly remove the given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return bool
   *   TRUE if the entity has been deleted. FALSE if
   *   an error occurred and the entity has not
   *   been deleted.
   */
  protected function tryDelete(EntityInterface $entity) {
    try {
      $entity->delete();
      return TRUE;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Returns the message to show the user after an item was deleted.
   *
   * @param int $count
   *   Count of deleted translations.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The item deleted message.
   */
  protected function getDeletedMessage($count) {
    return $this->formatPlural($count, 'Deleted @count @singular_entity_type.', 'Deleted @count @plural_entity_type.', [
      '@count' => $count,
      '@singular_entity_type' => $this->getEntityType()->getSingularLabel(),
      '@plural_entity_type' => $this->getEntityType()->getPluralLabel(),
    ]);
  }

}
