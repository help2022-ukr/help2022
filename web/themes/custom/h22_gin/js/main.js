/**
 * @file
 *
 * Extra main.js for the admin theme.
 */
/* eslint-disable func-names, no-mutable-exports, comma-dangle, strict */

'use strict';

(($, Drupal, drupalSettings) => {
  Drupal.behaviors.h22ginToolbarToggle = {
    attach: function attach(context) {
      // Toolbar toggle
      $('.page-wrapper', context).on('click', function (e) {
        if (window.matchMedia("(max-width: 767px)").matches && $('body', context).hasClass('toolbar-tray-open')) {
          $('#toolbar-bar > .toolbar-tab > .toolbar-icon.is-active', context).trigger('click');
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
