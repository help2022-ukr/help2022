<?php

namespace Drupal\h22_gin;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\gin\GinSettings;
use Drupal\user\UserDataInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Service to handle overridden user settings.
 */
class H22GinSettings extends GinSettings {
  /**
   * The user data service.
   *
   * @var \Drupal\user\UserDataInterface
   */
  protected $userData;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Return a massaged value from deprecated theme settings.
   *
   * @param string $name
   *   Name of the setting to check.
   * @param array|bool|mixed|null $value
   *   The value of the currently used setting.
   *
   * @return array|bool|mixed|null
   *   The value determined by a legacy setting.
   */
  private function handleLegacySettings($name, $value) {
    if ($name === 'enable_darkmode') {
      $value = (bool) $value;
    }
    if ($name === 'high_contrast_mode') {
      $value = (bool) $value;
    }
    if ($name === 'preset_accent_color') {
      $value = $value === 'claro_blue' ? 'blue' : $value;
    }
    if ($name === 'classic_toolbar') {
      $value = $value === TRUE || $value === 'true' ||  $value === '1' || $value === 1 ? 'classic' : $value;
    }
    return $value;
  }

  /**
   * Return the active admin theme.
   *
   * @return string
   *   The active admin theme name.
   */
  private function getAdminTheme() {
    $admin_theme = \Drupal::configFactory()->get('system.theme')->get('admin');
    if (empty($admin_theme)) {
      $admin_theme = \Drupal::configFactory()->get('system.theme')->get('default');
    }
    return $admin_theme;
  }

}
