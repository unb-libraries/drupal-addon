<?php

namespace Drupal\lib_unb_custom_entity\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldItemListInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates the UniqueField constraint.
 *
 * @package Drupal\lib_unb_custom_entity\Plugin\Validation\Constraint
 */
class UniqueValueValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Retrieve the entity manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   An entity manager object.
   */
  protected function entityTypeManager() {
    return $this->entityTypeManager;
  }

  /**
   * Create a new UniqueValueValidator instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   An entity manager object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity_type.manager'));
  }

  /**
   * {@inheritDoc}
   */
  public function validate($value, Constraint $constraint) {
    if ($value instanceof FieldItemListInterface) {
      if (!$value->getFieldDefinition()->getFieldStorageDefinition()->isMultiple()) {
        $this->validateProperty($value, $constraint);
      }
    }
  }

  /**
   * Validate that the given single-value field contains a unique value.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $field
   *   The definition of the field to validate.
   * @param \Drupal\lib_unb_custom_entity\Plugin\Validation\Constraint\UniqueValue $constraint
   *   The constraint.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function validateProperty(FieldItemListInterface $field, UniqueValue $constraint) {
    if (empty($value = $field->getValue())) {
      return;
    }

    $value = $value[0][array_keys($value[0])[0]];
    $entity = $field->getEntity();
    $entities = $this->loadExistingEntities($entity->getEntityTypeId(), $field->getName(), $value);

    if ($entity->isNew() && !empty($entities)) {
      $violator = $entities[array_keys($entities)[0]];
      $this->addViolation($value, $violator);
    }
    elseif (!$entity->isNew() && !empty($entities)) {
      $violator = $entities[array_keys($entities)[0]];
      if ($violator->id() !== $entity->id()) {
        $this->addViolation($value, $violator);
      }
    }
  }

  /**
   * Return an error message for an ambiguous field value.
   *
   * @param string $value
   *   The value.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An entity which uses the value.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   A translatable error message.
   */
  protected function addViolation(string $value, EntityInterface $entity) {
    $this->context->addViolation("@value is already used by @entity.", [
      '@value' => $value,
      '@entity' => $entity->label(),
    ]);
  }

  /**
   * Load entities of a given type where the given field equals the given value.
   *
   * @param string $entity_type_id
   *   An entity type ID.
   * @param string $field_id
   *   A field ID.
   * @param mixed $value
   *   A value.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entity objects.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function loadExistingEntities(string $entity_type_id, string $field_id, $value) {
    return $this->getStorage($entity_type_id)
      ->loadByProperties([$field_id => $value]);
  }

  /**
   * An entity storage handler.
   *
   * @param string $entity_type_id
   *   An entity type ID string.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   An entity storage handler object.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getStorage($entity_type_id) {
    return $this->entityTypeManager()
      ->getStorage($entity_type_id);
  }


}
