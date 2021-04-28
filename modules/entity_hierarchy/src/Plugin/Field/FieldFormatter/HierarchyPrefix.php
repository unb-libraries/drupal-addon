<?php

namespace Drupal\entity_hierarchy\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_hierarchy\Entity\HierarchicalInterface;

/**
 * Plugin implementation of the 'hierarchy_prefix' formatter.
 *
 * Renders a hierarchy level indicator as string-field prefix.
 *
 * @FieldFormatter(
 *   id = "hierarchy_prefix",
 *   label = @Translation("Hierarchy prefix"),
 *   field_types = {
 *     "string",
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 *
 * @package Drupal\lib_unb_custom_entity\Plugin\Field\FieldFormatter
 */
class HierarchyPrefix extends StringFormatter {

  /**
   * {@inheritDoc}
   */
  public static function defaultSettings() {
    $options = parent::defaultSettings();
    $options['sequence'] = '———';
    $options['prefix'] = '';
    $options['suffix'] = '';
    return $options;
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['sequence'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Sequence'),
      '#default_value' => $this->getSetting('sequence'),
    ];

    $form['prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Prefix'),
      '#default_value' => $this->getSetting('prefix'),
    ];

    $form['suffix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Suffix'),
      '#default_value' => $this->getSetting('suffix'),
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  protected function viewValue(FieldItemInterface $item) {
    $render = parent::viewValue($item);
    $value = $render['#context']['value'];

    $entity = $item->getEntity();
    if ($entity instanceof HierarchicalInterface) {
      $sequence = $this->viewPrefix($entity);
      $prefix = $this->getSetting('prefix');
      $suffix = $this->getSetting('suffix');

      $value = "{$prefix}{$sequence}{$suffix} {$value}";
    }

    $render['#context']['value'] = $value;
    return $render;
  }

  /**
   * Build the prefix that indicates the nesting level within the hierarchy.
   *
   * @param \Drupal\entity_hierarchy\Entity\HierarchicalInterface $entity
   *   A hierarchical entity.
   *
   * @return string
   *   A string.
   */
  protected function viewPrefix(HierarchicalInterface $entity) {
    $sequence = '';
    while ($entity->getSuperior()) {
      $sequence .= $this->getSetting('sequence');
      $entity = $entity->getSuperior();
    }
    return $sequence;
  }

}
