<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\ContentEntityBase;

/**
 * Base class for content entities that represent REST resources.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
abstract class ContentEntityResource extends ContentEntityBase {

  /**
   * {@inheritDoc}
   */
  public function toUrl($rel = 'canonical', array $options = []) {
    /** @var \Drupal\Core\Url $url */
    $url = parent::toUrl($rel, $options);
    if (array_key_exists('format', $options)) {
      $url->setOption('query', [
        '_format' => $options['format'],
      ]);
    }
    return $url;
  }

}
