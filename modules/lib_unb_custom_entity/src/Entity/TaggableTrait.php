<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\TermInterface;

/**
 * Trait to make content entities taggable.
 *
 * @package Drupal\lib_nb_custom_entity\Entity
 */
trait TaggableTrait {

  /**
   * {@inheritDoc}
   */
  public function getTags($vid = '') {
    /** @var \Drupal\lib_unb_custom_entity\Entity\Storage\TaggableContentEntityStorageInterface $storage */
    $storage = $this->getStorage();

    $tags = $this->get($storage->getTagField($vid)->getName())
      ->referencedEntities();
    $tag_ids = array_map(function (Term $tag) {
      return $tag->id();
    }, $tags);

    return array_combine(array_values($tag_ids), array_values($tags));
  }

  /**
   * {@inheritDoc}
   */
  public function getTagNames($vid = '') {
    return array_map(function (TermInterface $tag) {
      return $tag->getName();
    }, $this->getTags($vid));
  }

  /**
   * {@inheritDoc}
   */
  public function setTags(array $tags, $vid = '') {
    /** @var \Drupal\lib_unb_custom_entity\Entity\Storage\TaggableContentEntityStorageInterface $storage */
    $storage = $this->getStorage();
    return $this->set($storage->getTagField($vid)->getName(), $tags);
  }

  /**
   * {@inheritDoc}
   *
   * @throws \Exception
   */
  public function addTag(Term $tag, $vid = '') {
    $tags = $this->getTags($vid);
    $tags[$tag->id()] = $tag;
    return $this->setTags($tags, $vid);
  }

  /**
   * {@inheritDoc}
   *
   * @throws \Exception
   */
  public function removeTag(Term $tag, $vid = '') {
    $tags = $this->getTags($vid);
    unset($tags[$tag->id()]);
    return $this->setTags($tags, $vid);
  }

  /**
   * {@inheritDoc}
   *
   * @throws \Exception
   */
  public function clearTags($vid = '') {
    return $this->setTags([], $vid);
  }

  /**
   * Create the field definition for a tag within the given vocabulary.
   *
   * @param string $vid
   *   The vocabulary ID.
   * @param array $options
   *   (optional) Array of options accepting the following keys:
   *   - field_id: (string) ID of the field to create.
   *   Defaults to "tags_VID".
   *   - label: (string) Label of the field to create.
   *   Defaults to "VOCABULARY_LABEL tags".
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   A field definition.
   */
  private static function tagFieldDefinition($vid, $options = []) {
    $fields = [];
    /** @noinspection PhpUnhandledExceptionInspection */
    $vocabulary = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_vocabulary')
      ->load($vid);

    $options += [
      'field_id' => sprintf('%s_%s', TaggableInterface::FIELD_TAGS, $vid),
      'label' => sprintf("%s tags", $vocabulary ? $vocabulary->label() : ucfirst($vid)),
    ];

    $fields[$options['field_id']] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t($options['label']))
      ->setRequired(FALSE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'taxonomy_term',
        'handler_settings' => [
          'target_bundles' => [
            $vid => $vid,
          ],
        ],
      ]);

    return $fields;
  }

}
