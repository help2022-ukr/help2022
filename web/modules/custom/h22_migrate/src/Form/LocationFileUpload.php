<?php

namespace Drupal\h22_migrate\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate_tools\MigrateBatchExecutable;

/**
 * Class LocationFileUpload.
 *
 * @package Drupal\h22_migrate\Form
 */
class LocationFileUpload extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'location_file_upload';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['file_upload'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload source file'),
      '#upload_location' => 'private://data/locations/',
      '#upload_validators' => [
        'file_validate_extensions' => [
          'csv',
        ],
      ],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Upload'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $file = _file_save_upload_from_form($form['file_upload'], $form_state, 0);
    // @TODO: Validate headers/columns from CSV to have correct order.
    $form_state->setValue('file_upload', $file);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $migration_plugin_manager = \Drupal::service('plugin.manager.migration');
    // Invalidate plugin cache so we can register our new migration dynamically.
    Cache::invalidateTags(['migration_plugins']);
    /** @var \Drupal\migrate\Plugin\MigrationInterface $migration_plugin */
    $derivative_id = str_replace('.csv', '', $form_state->getValue('file_upload')->getFilename());
    $migration_plugin = $migration_plugin_manager->createInstance('locations:' . $derivative_id);
    $migrateMessage = new MigrateMessage();

    $options = [
      'limit' => 0,
      'update' => 0,
      'force' => 0,
    ];

    $executable = new MigrateBatchExecutable($migration_plugin, $migrateMessage, $options);
    $executable->batchImport();
  }

}
