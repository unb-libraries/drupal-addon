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
    if (array_key_exists('format', $options) && $this->respondsTo($format = $options['format'])) {
      $url->setOption('query', [
        '_format' => $format,
      ]);
    }
    return $url;
  }

  /**
   * Whether the entity can be queried in the given format.
   *
   * @param string $format
   *   The format, e.g. 'html' or 'json'.
   *
   * @return bool
   *   TRUE if a route provider exists for the given format
   *   and the entity. FALSE otherwise.
   */
  protected function respondsTo($format) {
    $route_providers = $this->getEntityType()
      ->getHandlerClasses()['route_provider'];
    return array_key_exists($format, $route_providers);
  }

}
