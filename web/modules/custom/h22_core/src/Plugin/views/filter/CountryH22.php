<?php

namespace Drupal\h22_core\Plugin\views\filter;

use Drupal\address\Plugin\views\filter\CountryAwareInOperatorBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Filter by country.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("country_h22")
 */
class CountryH22 extends CountryAwareInOperatorBase {

  /**
   * {@inheritdoc}
   */
  public function getValueOptions() {
    if (!isset($this->valueOptions)) {
      $this->valueOptions = $this->getAvailableCountries();
    }

    return $this->valueOptions;
  }

  /**
   * {@inheritdoc}
   */
  protected function getAvailableCountries(EntityTypeInterface $entity_type = NULL, $field_name = NULL) {
    $countries = parent::getAvailableCountries($entity_type, $entity_type, $field_name);
    $countries = [
      'neighbors' => $this->t('Neighboring Countries'),
      '' => $this->t('---'),
    ] + $countries;

    return $countries;
  }

  /**
   * {@inheritdoc}
   */
  protected function opSimple() {
    if (empty($this->value)) {
      return;
    }
    $this->ensureMyTable();

    // Make sure we cover our custom case.
    if (!empty($this->value) && $this->value[0] === 'neighbors') {
      $value = ['RO', 'MD', 'HU', 'SK', 'PL'];
    }
    else {
      $value = array_values($this->value);
    }

    // We use array_values() because the checkboxes keep keys and that can cause
    // array addition problems.
    $this->query->addWhere($this->options['group'], "$this->tableAlias.$this->realField", $value, $this->operator);
  }

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $form['value'] = [];
    $options = [];

    $exposed = $form_state->get('exposed');
    if (!$exposed) {
      // Add a select all option to the value form.
      $options = ['all' => $this->t('Select all')];
    }

    $this->getValueOptions();
    $options += $this->valueOptions;
    $default_value = (array) $this->value;

    $which = 'all';
    if (!empty($form['operator'])) {
      $source = ':input[name="options[operator]"]';
    }
    if ($exposed) {
      $identifier = $this->options['expose']['identifier'];

      if (empty($this->options['expose']['use_operator']) || empty($this->options['expose']['operator_id'])) {
        // exposed and locked.
        $which = in_array($this->operator, $this->operatorValues(1)) ? 'value' : 'none';
      }
      else {
        $source = ':input[name="' . $this->options['expose']['operator_id'] . '"]';
      }

      if (!empty($this->options['expose']['reduce'])) {
        $options = $this->reduceValueOptions();

        if (!empty($this->options['expose']['multiple']) && empty($this->options['expose']['required'])) {
          $default_value = [];
        }
      }

      if (empty($this->options['expose']['multiple'])) {
        if (empty($this->options['expose']['required']) && (empty($default_value) || !empty($this->options['expose']['reduce'])) || isset($this->options['value']['all'])) {
          $default_value = 'neighbors';
        }
        elseif (empty($default_value)) {
          $keys = array_keys($options);
          $default_value = array_shift($keys);
        }
        else {
          $copy = $default_value;
          $default_value = array_shift($copy);
        }
      }
    }

    if ($which == 'all' || $which == 'value') {
      $form['value'] = [
        '#type' => $this->valueFormType,
        '#title' => $this->valueTitle,
        '#options' => $options,
        '#default_value' => $default_value,
        // These are only valid for 'select' type, but do no harm to checkboxes.
        '#multiple' => TRUE,
        // The value options can be a multidimensional array if the value form
        // type is a select list, so make sure that they are counted correctly.
        '#size' => min(count($options, COUNT_RECURSIVE), 8),
      ];
      $user_input = $form_state->getUserInput();
      if ($exposed && !isset($user_input[$identifier])) {
        $user_input[$identifier] = $default_value;
        $form_state->setUserInput($user_input);
      }

      if ($which == 'all') {
        if (!$exposed && (in_array($this->valueFormType, ['checkbox', 'checkboxes', 'radios', 'select']))) {
          $form['value']['#prefix'] = '<div id="edit-options-value-wrapper">';
          $form['value']['#suffix'] = '</div>';
        }
        // Setup #states for all operators with one value.
        foreach ($this->operatorValues(1) as $operator) {
          $form['value']['#states']['visible'][] = [
            $source => ['value' => $operator],
          ];
        }
      }
    }  }

}
