<?php

namespace Drupal\lib_unb_custom_entity\Entity;

use Drupal\Core\Entity\EntityInterface;

/**
 * Renders an entity as a table listing of its properties.
 *
 * @package Drupal\lib_unb_custom_entity\Entity
 */
abstract class TableViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritDoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $page = parent::view($entity, $view_mode, $langcode);

    $page['table'] = [
      '#type' => 'table',
      '#header' => $this->displayHeader($entity)
      ? $this->buildHeader()
      : [],
      '#rows' => $this->buildRows($entity),
    ];

    return $page;
  }

  /**
   * Whether to display a table header.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to render.
   *
   * @return bool
   *   TRUE if the header should be displayed. FALSE if
   *   it should be hidden.
   */
  protected function displayHeader(EntityInterface $entity) {
    return FALSE;
  }

  /**
   * Build the header, should it be displayed.
   *
   * @return array
   *   An array of the form COLUMN_ID => COLUMN_HEADER_LABEL.
   */
  protected function buildHeader() {
    return [
      'label' => $this->t('Property'),
      'value' => $this->t('Value'),
    ];
  }

  /**
   * Build the rows of the table.
   *
   * Each row contains one property of the
   * the entity to be rendered.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to render.
   *
   * @return array
   *   An array of the form COLUMN_ID => COLUMN_VALUE.
   */
  protected function buildRows(EntityInterface $entity) {
    $rows = [];

    $labels = $this->buildLabelColumn($entity);
    $values = $this->buildValueColumn($entity);

    foreach (array_keys($labels) as $field_id) {
      $rows[] = [
        'label' => $labels[$field_id],
        'value' => $values[$field_id],
      ];
    }

    return $rows;
  }

  /**
   * Build the labels for each property of the entity to be rendered.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to be rendered.
   *
   * @return array
   *   An array of the form PROPERTY_ID => PROPERTY_LABEL.
   */
  abstract protected function buildLabelColumn(EntityInterface $entity);

  /**
   * Build the values for each property of the entity to be rendered.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to be rendered.
   *
   * @return array
   *   An array of the form PROPERTY_ID => PROPERTY_VALUE.
   *   Property IDs must match the array keys returned
   *   by @see \Drupal\lib_unb_custom_entity\Entity\TableViewBuilder::buildLabelColumn().
   */
  abstract protected function buildValueColumn(EntityInterface $entity);

}
