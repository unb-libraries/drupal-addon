<?php

namespace Drupal\lib_unb_custom_entity\FieldObserver;

use SplSubject;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\changed_fields\EntitySubject;
use Drupal\changed_fields\ObserverInterface;

/**
 * Field observer to detect changes to any revisionable entity fields.
 *
 * This can be used to auto-create new entity revisions when any of
 * its revisionable fields has been updated.
 *
 * @package Drupal\lib_unb_custom_entity\Field
 */
class RevisionableEntityFieldObserver implements ObserverInterface {

  /**
   * An entity field manager service instance.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $fieldManager;

  /**
   * The entity type this observes monitors.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface
   */
  protected $entityType;

  /**
   * Retrieve an entity field manager service instance.
   *
   * @return \Drupal\Core\Entity\EntityFieldManagerInterface
   *   An entity field manager object.
   */
  protected function fieldManager() {
    if (!isset($this->fieldManager)) {
      $this->fieldManager = \Drupal::service('entity_field.manager');
    }
    return $this->fieldManager;
  }

  /**
   * Retrieve the entity type this observer monitors.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   An entity type object.
   */
  protected function getEntityType() {
    return $this->entityType;
  }

  /**
   * Create a new RevisionableEntityFieldObserver instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type this observer should monitor.
   */
  public function __construct(EntityTypeInterface $entity_type) {
    $this->entityType = $entity_type;
  }

  /**
   * {@inheritDoc}
   */
  public function getInfo() {
    $revisionable_fields = [];

    $revisionable_field_filter = \Closure::fromCallable([$this, 'isRevisionableField']);
    if ($bundle_type_id = $this->getEntityType()->getBundleEntityType()) {
      $bundles = \Drupal::entityTypeManager()->getStorage($bundle_type_id)->loadMultiple();
      foreach ($bundles as $bundle) {
        $fields = $this->fieldManager()->getFieldDefinitions($this->getEntityType()->id(), $bundle->id());
        $revisionable_fields[$bundle->id()] = array_keys(array_filter($fields, $revisionable_field_filter));
      }
    }
    else {
      $fields = $this->fieldManager()->getFieldDefinitions($this->getEntityType()->id(), $this->getEntityType()->id());
      $revisionable_fields = array_keys(array_filter($fields, $revisionable_field_filter));
    }

    return [
      $this->getEntityType()->id() => $revisionable_fields,
    ];
  }

  /**
   * Whether the given field is revisionable.
   *
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition.
   *
   * @return bool
   *   TRUE if the field definition describes a revisionable field.
   *   FALSE otherwise.
   */
  protected function isRevisionableField(FieldDefinitionInterface $field_definition) {
    return $field_definition->getFieldStorageDefinition()->isRevisionable();
  }

  /**
   * {@inheritDoc}
   */
  public function update(SplSubject $subject) {
    if ($subject instanceof EntitySubject) {
      if (!empty($subject->getChangedFields())) {
        $entity = $subject->getEntity();
        $entity->setNewRevision();
        if ($entity instanceof RevisionLogInterface) {
          $message = t("Updated @field_names.", [
            '@field_names' => implode(', ', array_keys($subject->getChangedFields())),
          ]);
          $entity->setRevisionLogMessage($message);
        }
      }
    }
  }

}
