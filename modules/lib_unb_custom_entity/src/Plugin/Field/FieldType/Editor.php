<?php

namespace Drupal\lib_unb_custom_entity\Plugin\Field\FieldType;

/**
 * Defines the 'editor' entity field type.
 *
 * This builds upon the "entity_reference" field type and always references
 * the "user" entity. Similar to the "changed" field type, the field value will
 * change to the last user who edited the entity. An initial value is assigned
 * upon creation of the entity.
 *
 * @FieldType(
 *   id = "editor",
 *   label = @Translation("Editor"),
 *   description = @Translation("A field referencing the last user who edited an entity."),
 *   category = @Translation("Reference"),
 *   no_ui = TRUE,
 *   cardinality = 1,
 *   default_formatter = "entity_reference_label"
 * )
 */
class Editor extends Creator {

}
