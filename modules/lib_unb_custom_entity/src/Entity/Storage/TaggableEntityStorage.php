<?php

namespace Drupal\lib_unb_custom_entity\Entity\Storage;

use Drupal\lib_unb_custom_entity\Entity\Storage\TaggableContentEntityStorageInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\lib_unb_custom_entity\Entity\TaggableInterface;

/**
 * Storage handler for entities that reference taxonomy term entities.
 *
 * @package Drupal\lib_nb_custom_entity\Entity
 */
class TaggableEntityStorage extends SqlContentEntityStorage implements TaggableContentEntityStorageInterface {

  /**
   * Retrieve an entity field manager.
   *
   * @return \Drupal\Core\Entity\EntityFieldManagerInterface
   *   An entity field manager instance.
   */
  protected function getEntityFieldManager() {
    /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $field_manager */
    $field_manager = \Drupal::service('entity_field.manager');
    return $field_manager;
  }

  /**
   * Retrieve the entity type manager service.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   An entity type manager instance.
   */
  protected function getEntityTypeManager() {
    return \Drupal::entityTypeManager();
  }

  /**
   * Retrieve the storage handler for taxonomy term entities.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   An entity storage handler instance.
   */
  protected function getTagStorage() {
    return $this->getEntityTypeManager()
      ->getStorage('taxonomy_term');
  }

  /**
   * {@inheritDoc}
   */
  public function loadByTagNames(array $names) {
    $query = $this->getTagStorage()
      ->getQuery()
      ->condition('name', $names, 'IN');
    $tags = $this->getTagStorage()->loadMultiple($query->execute());
    return $this->loadByTags($tags);
  }

  /**
   * {@inheritDoc}
   */
  public function loadByTags(array $tags, $vid = '') {
    $query = $this->getQuery('OR');
    if ($vid) {
      foreach ($tags as $tag_id => $tag) {
        $query->condition($this->getTagField($vid)->getName(), $tag_id, 'CONTAINS');
      }
    }
    else {
      foreach ($this->getTagFields() as $tag_field) {
        foreach ($tags as $tag_id => $tag) {
          $query->condition($tag_field->getName(), $tag_id, 'CONTAINS');
        }
      }
    }
    return $this->loadMultiple($query->execute());
  }

  /**
   *
   * {@inheritDoc}
   */
  public function getTagField($vid) {
    foreach ($this->getTagFields() as $field_id => $field_definition) {
      $settings = $field_definition->getSettings();
      if (array_key_exists('handler_settings', $settings) && array_key_exists('target_bundles', $settings['handler_settings']) && array_key_exists($vid, $settings['handler_settings']['target_bundles'])) {
        return $field_definition;
      }
    }
    return NULL;
  }

  /**
   * Retrieve any field that references taxonomy term entities.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   An array of field definitions.
   */
  protected function getTagFields() {
    $tag_field_definitions = [];
    $field_definitions = $this->getEntityFieldManager()
      ->getFieldDefinitions($this->getEntityTypeId(), $this->getEntityTypeId());
    foreach ($field_definitions as $field_id => $field_definition) {
      if ($field_definition->getType() === 'entity_reference' && $field_definition->getSetting('target_type') === 'taxonomy_term') {
        $tag_field_definitions[$field_id] = $field_definition;
      }
    }
    return $tag_field_definitions;
  }

  /**
   * {@inheritDoc}
   */
  public function loadRetired() {
    // TODO: Implement loadRetired() method.
  }

}
