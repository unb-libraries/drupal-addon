<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\taxonomy\Entity\Term;

/**
 * Interface for entities that feature taxonomy term categorization.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
interface TaggableInterface extends EntityInterface {

  const FIELD_TAGS = 'tags';

  /**
   * Retrieve all assigned tags.
   *
   * @param string $vid
   *   The vocabulary ID.
   *
   * @return \Drupal\taxonomy\Entity\Term[]
   *   An array of taxonomy term entities.
   */
  public function getTags($vid = '');

  /**
   * Assign a collection of tags.
   *
   * @param string $vid
   *   The vocabulary ID.
   * @param \Drupal\taxonomy\Entity\Term[] $tags
   *   An array of taxonomy term entities.
   */
  public function setTags(array $tags, $vid = '');

  /**
   * Assign the given taxonomy term.
   *
   * @param string $vid
   *   The vocabulary ID.
   * @param \Drupal\taxonomy\Entity\Term $tag
   *   A taxonomy term entity.
   */
  public function addTag(Term $tag, $vid = '');

  /**
   * Remove the given taxonomy term.
   *
   * @param string $vid
   *   The vocabulary ID.
   * @param \Drupal\taxonomy\Entity\Term $tag
   *   A taxonomy term entity.
   */
  public function removeTag(Term $tag, $vid = '');

  /**
   * Remove all assigned taxonomy terms.
   *
   * @param string $vid
   *   The vocabulary ID.
   */
  public function clearTags($vid = '');

}
