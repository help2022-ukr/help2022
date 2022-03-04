<?php

namespace Drupal\h22_migrate\Plugin;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\File\FileSystem;

/**
 * Expose migration entities in the active config store as derivative plugins.
 */
class LocationDeriver extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($definition) {
    /** @var FileSystem $file_system */
    $file_system = \Drupal::service('file_system');
    $files = $file_system->scanDirectory('private://data/locations/', '/.*/');
    /** @var \Drupal\migrate_plus\Entity\MigrationInterface $migration */
    foreach ($files as $file_path => $file) {
      // @TODO Improve filename sanitization and make it a reusable function.
      $derivative_id = str_replace('.csv', '', $file->filename);
      $this->derivatives[$derivative_id] = $this->getDerivativeValues($definition, $file_path);
    }
    return $this->derivatives;
  }


  /**
   * Creates a derivative definition for each available language.
   *
   * @param array $definition
   * @param string $file_path
   *
   * @return array
   */
  protected function getDerivativeValues(array $definition, string $file_path) {
    $definition['source']['path'] = $file_path;

    return $definition;
  }

}
