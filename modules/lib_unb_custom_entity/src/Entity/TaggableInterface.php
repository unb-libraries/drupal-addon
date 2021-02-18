<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityInterface;

/**
 * Interface for entities that feature taxonomy term categorization.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
interface TaggableInterface extends EntityInterface {

  const FIELD_TAGS = 'tags';

  /**
   * Whether the entity is assigned the given tag.
   *
   * @param string|int|\Drupal\taxonomy\TermInterface $tag
   *   A tag name, ID, or entity object.
   * @param string $vid
   *   (optional) The vocabulary which the tag belongs to.
   *
   * @return bool
   *   TRUE if the given tag is assigned to the entity.
   *   FALSE if it is not.
   */
  public function hasTag($tag, $vid = '');

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
   * Retrieve all named of assigned tags.
   *
   * @param string $vid
   *   The vocabulary ID.
   *
   * @return array
   *   An array of strings.
   */
  public function getTagNames($vid = '');

  /**
   * Assign a collection of tags.
   *
   * @param \Drupal\taxonomy\Entity\Term[] $tags
   *   An array of taxonomy term entities.
   * @param string $vid
   *   The vocabulary ID.
   */
  public function setTags(array $tags, $vid = '');

  /**
   * Assign the given taxonomy term.
   *
   * @param string|int|\Drupal\taxonomy\TermInterface $tag
   *   The tag name, ID, or entity object to add.
   * @param string $vid
   *   (optional) The vocabulary ID.
   */
  public function addTag($tag, $vid = '');

  /**
   * Remove the given taxonomy term.
   *
   * @param string|int|\Drupal\taxonomy\Entity\Term $tag
   *   A tag name, ID, or entity object.
   * @param string $vid
   *   The vocabulary ID.
   */
  public function removeTag($tag, $vid = '');

  /**
   * Remove all assigned taxonomy terms.
   *
   * @param string $vid
   *   The vocabulary ID.
   */
  public function clearTags($vid = '');

}
