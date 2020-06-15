<?php

namespace Drupal\lib_unb_custom_entity\Entity\Storage;

use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Storage handler for entities that reference taxonomy term entities.
 *
 * @package Drupal\lib_nb_custom_entity\Entity
 */
class TaggableEntityStorage extends SqlContentEntityStorage implements TaggableContentEntityStorageInterface {

  /**
   * The active database connection.
   *
   * @return \Drupal\Core\Database\Connection
   *   A database connection object.
   */
  protected function database() {
    return $this->database;
  }

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
    return $this->entityTypeManager;
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
  public function loadByTags(array $tags, array $options = []) {
    $options += [
      'include_legacy' => FALSE,
    ];

    $query = $this->getQuery('OR');
    foreach ($tags as $tag_id => $tag) {
      if ($field = $this->getTagField($tag->bundle())) {
        $query->condition($field->getName(), $tag_id, 'CONTAINS');
      }
    }

    $entities = $this->loadMultiple($query->execute());
    if ($this->getEntityType()->isRevisionable() && $options['include_legacy']) {
      $entities += $this->loadRetired($tags);
    }

    return $entities;
  }

  /**
   * {@inheritDoc}
   */
  public function getTagField($vid = '') {
    foreach ($this->getTagFields() as $field_id => $field_definition) {
      $settings = $field_definition->getSettings();
      if (!$vid || array_key_exists($vid, $settings['handler_settings']['target_bundles'])) {
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
  public function loadRetired(array $tags) {
    $previously_or_currently_tagged_entity_ids = array_unique(array_map(function (RevisionableInterface $revision) {
      return $revision->id();
    }, $this->loadTaggedRevisions($tags)));

    $currently_tagged_entities = $this->loadByTags($tags);
    $previously_tagged_entity_ids = array_diff(
      array_values($previously_or_currently_tagged_entity_ids), array_keys($currently_tagged_entities));

    return $this->loadMultiple($previously_tagged_entity_ids);
  }

  /**
   * Load entity revisions associated with one of the given tags.
   *
   * @param \Drupal\taxonomy\Entity\Term[] $tags
   *   An array of taxonomy term entities.
   *
   * @return \Drupal\Core\Entity\RevisionableInterface[]
   *   An array of entity revisions.
   */
  private function loadTaggedRevisions(array $tags) {
    $revision_ids = $this->buildTaggedRevisionsQuery($tags)
      ->fetchCol();
    return $this->loadMultipleRevisions($revision_ids);
  }

  /**
   * Build a query to retrieve all entity revisions associated with one of the given tags.
   *
   * @param \Drupal\taxonomy\Entity\Term[] $tags
   *   An array of taxonomy term entities.
   *
   * @return \Drupal\Core\Database\StatementInterface
   *   A query object.
   */
  private function buildTaggedRevisionsQuery(array $tags) {
    $vocabularies = [];
    foreach ($tags as $tag) {
      $vocabularies[$tag->bundle()][] = $tag->id();
    }

    $sub_queries = [];
    foreach ($vocabularies as $vid => $tag_ids) {
      $base_query = "SELECT revision_id FROM {@table_name} WHERE @target_column IN (@tag_ids)";

      // TODO: Implement this using Drupal\Core\Entity\Sql\SqlEntityStorageInterface::getTableMapping() and Drupal\Core\Entity\Sql\DefaultTableMapping::getFieldTableName().
      $field_name = $this->getTagField($vid)->getName();
      $placeholders = [
        '@table_name' => $this->getEntityType()->getRevisionTable() . '__' . $field_name,
        '@target_column' => $field_name . '_target_id',
        '@tag_ids' => implode(',', $tag_ids),
      ];
      $compiled_query = str_replace(array_keys($placeholders), array_values($placeholders), $base_query);
      $sub_queries[] = $compiled_query;
    }

    $sql = implode(' UNION ', $sub_queries);
    return $this->database()
      ->query($sql);
  }

}
