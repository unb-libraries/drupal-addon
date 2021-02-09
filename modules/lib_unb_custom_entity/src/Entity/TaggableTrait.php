<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
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
   * {@inheritDoc
   */
  public function hasTag($tag, $vid = '') {
    $has_tag = FALSE;
    if (is_string($tag)) {
      $has_tag = in_array($tag, $this->getTagNames($vid));
    }
    else {
      $tags = $this->getTags($vid);
      if (is_int($tag)) {
        $has_tag = array_key_exists($tag, $tags);
      }
      elseif ($tag instanceof TermInterface) {
        $has_tag = array_key_exists($tag->id(), $tags);
      }
    }
    return $has_tag;
  }

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
  public function addTag($tag, $vid = '') {
    if (is_string($tag)) {
      $query = $this->tagStorage()->getQuery()
        ->condition('name', $tag);
      if ($vid) {
        $query->condition('vid', $vid);
      }

      if (!empty($tag_ids = $this->tagStorage()->loadMultiple($query->execute()))) {
        return $this->addTag($tag_ids[array_keys($tag_ids)[0]], $vid);
      }
    }
    else {
      $tags = $this->getTags($vid);
      if (is_int($tag)) {
        return $this->addTag($this->tagStorage()->load($tag), $vid);
      }
      elseif ($tag instanceof TermInterface) {
        $tags[$tag->id()] = $tag;
        return $this->setTags($tags, $vid);
      }
    }
  }

  /**
   * {@inheritDoc}
   *
   * @throws \Exception
   */
  public function removeTag($tag, $vid = '') {
    if(is_string($tag)) {
      $query = $this->tagStorage()->getQuery()
        ->condition('name', $tag);
      if ($vid) {
        $query->condition('vid', $vid);
      }

      if (!empty($tag_ids = $this->tagStorage()->loadMultiple($query->execute()))) {
        return $this->removeTag($tag_ids[array_keys($tag_ids)[0]], $vid);
      }
    }
    else {
      $tags = $this->getTags($vid);
      if (is_int($tag)) {
        return $this->removeTag($this->tagStorage()->load($tag), $vid);
      }
      elseif ($tag instanceof TermInterface) {
        unset($tags[$tag->id()]);
        return $this->setTags($tags, $vid);
      }
    }
  }

  /**
   * Retrieve the taxonomy term storage handler.
   *
   * @return \Drupal\taxonomy\TermStorageInterface
   *   An entity storage handler for taxonomy term entities.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function tagStorage() {
    /** @noinspection PhpUnhandledExceptionInspection */
    /** @var \Drupal\taxonomy\TermStorageInterface $storage */
    $storage = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term');
    return $storage;
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
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to which the field will be attached.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface
   *   A field definition.
   */
  private static function tagFieldDefinition(string $vid, EntityTypeInterface $entity_type) {
    return BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Tags'))
      ->setRequired(FALSE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setRevisionable($entity_type->isRevisionable())
      ->setSettings([
        'target_type' => 'taxonomy_term',
        'handler_settings' => [
          'target_bundles' => [
            $vid => $vid,
          ],
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'weight' => 40,
      ])
      ->setDisplayOptions('view', [
        'weight' => 40,
      ]);
  }

}
