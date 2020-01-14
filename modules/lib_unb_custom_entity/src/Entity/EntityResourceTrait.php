<?php

namespace Drupal\lib_unb_custom_entity\Entity;

trait EntityResourceTrait {

  /**
   * {@inheritDoc}
   */
  public function toUrl($rel = 'canonical', array $options = []) {
    /** @var \Drupal\Core\Url $url */
    $url = parent::toUrl($rel, $options);
    $format = $options['format'] ?: $this->defaultFormat();
    if ($format !== $this->defaultFormat()) {
      $url->setOption('query', [
        '_format' => $format,
      ]);
    }
    return $url;
  }

  /**
   * Retrieve the default format.
   *
   * @return string
   *   A string.
   */
  protected function defaultFormat() {
    return 'html';
  }

}