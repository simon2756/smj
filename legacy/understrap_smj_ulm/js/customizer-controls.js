/*!
 * Understrap v1.0.0 (https://github.com/FelixBetz/UnderstrapSmjUlm)
 * Copyright 2013-2026 Felix Betz (https://github.com/FelixBetz)
 * Licensed under GPL-3.0 (https://www.gnu.org/licenses/gpl-3.0.html)
 */
(function () {
    'use strict';

    /**
     * Scripts within the customizer controls window.
     *
     * Contextually shows the navbar type setting and informs the preview
     * when users open or close the front page sections section.
     */

    (function () {
      wp.customize.bind('ready', function () {
        // Only show the navbar type setting when running Bootstrap 5.
        wp.customize('understrap_bootstrap_version', function (setting) {
          wp.customize.control('understrap_navbar_type', function (control) {
            var visibility = function visibility() {
              if ('bootstrap5' === setting.get()) {
                control.container.slideDown(180);
              } else {
                control.container.slideUp(180);
              }
            };
            visibility();
            setting.bind(visibility);
          });
        });
      });
    })();

})();
//# sourceMappingURL=customizer-controls.js.map
