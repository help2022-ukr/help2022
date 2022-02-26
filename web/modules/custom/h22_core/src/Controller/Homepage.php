<?php

namespace Drupal\h22_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Controller routines for page example routes.
 */
class Homepage extends ControllerBase {
  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'h22_core';
  }

  /**
   * Constructs a simple page.
   */
  public function welcome() {
    return [
      '#theme' => 'h22_core_homepage',
    ];
  }

}
