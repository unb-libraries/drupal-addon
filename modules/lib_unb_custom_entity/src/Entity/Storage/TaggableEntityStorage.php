<?php

namespace Drupal\lib_nb_custom_entity\Entity;

use Drupal\lib_unb_custom_entity\Entity\Storage\TaggableContentEntityStorageInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

class TaggableEntityStorage extends SqlContentEntityStorage implements TaggableContentEntityStorageInterface {

  /**
   * @inheritDoc
   */
  public function loadByVocabularies(array $vids) {
    // TODO: Implement loadByVocabularies() method.
  }

  /**
   * @inheritDoc
   */
  public function loadByTags(array $tags) {
    // TODO: Implement loadByTags() method.
  }

  /**
   * @inheritDoc
   */
  public function loadByTagNames(array $names) {
    // TODO: Implement loadByTagNames() method.
  }

  /**
   * @inheritDoc
   */
  public function loadRetired() {
    // TODO: Implement loadRetired() method.
  }

}
