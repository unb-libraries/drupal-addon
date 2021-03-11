<?php

namespace Drupal\lib_unb_custom_entity\Plugin\Field\FieldType;

use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;

/**
 * Defines the 'creator' entity field type.
 *
 * This builds upon the "entity_reference" field type and always references
 * the "user" entity. Similar to the "created" field type, any field value will
 * persist once initialized. An initial value is assigned upon creation of the
 * entity.
 *
 * @FieldType(
 *   id = "creator",
 *   label = @Translation("Creator"),
 *   description = @Translation("An entity field unchangeably referencing a user entity."),
 *   category = @Translation("Reference"),
 *   no_ui = TRUE,
 *   cardinality = 1,
 *   default_formatter = "entity_reference_label"
 * )
 */
class Creator extends EntityReferenceItem {

}
