<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityInterface;

/**
 * Interface for entities that feature taxonomy term categorization.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
interface TaggableInterface extends EntityInterface {

  /**
   * Retrieve all assigned tags.
   *
   * @return \Drupal\taxonomy\Entity\Term[]
   *   An array of taxonomy term entities.
   */
  public function getTags();

  /**
   * Assign a collection of tags.
   *
   * @param \Drupal\taxonomy\Entity\Term[] $tags
   *   An array of taxonomy term entities.
   */
  public function setTags($tags);

  /**
   * Assign the given taxonomy term.
   *
   * @param \Drupal\taxonomy\Entity\Term $tag
   *   A taxonomy term entity.
   */
  public function addTag($tag);

  /**
   * Remove the given taxonomy term.
   *
   * @param \Drupal\taxonomy\Entity\Term $tag
   *   A taxonomy term entity.
   */
  public function removeTag($tag);

  /**
   * Remove all assigned taxonomy terms.
   */
  public function clearTags();

}
