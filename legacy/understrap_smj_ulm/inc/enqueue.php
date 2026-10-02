<?php
/**
 * Understrap enqueue scripts
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'understrap_scripts' ) ) {
	/**
	 * Load theme's JavaScript and CSS sources.
	 */
	function understrap_scripts() {
		// Get the theme data.
		$the_theme         = wp_get_theme();
		$theme_version_raw = $the_theme->get( 'Version' );
		$theme_version     = is_string( $theme_version_raw ) ? $theme_version_raw : '1.0.0';
		$suffix            = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Grab asset urls.
		$theme_styles  = "/css/theme{$suffix}.css";
		$theme_scripts = "/js/theme{$suffix}.js";

		$css_version = $theme_version . '.' . filemtime( get_template_directory() . $theme_styles );
		wp_enqueue_style( 'understrap-styles', get_template_directory_uri() . $theme_styles, array(), $css_version );
		understrap_nav_hover_inline_styles();

		// Fix that the offcanvas close icon is hidden behind the admin bar.
		if ( is_admin_bar_showing() ) {
			understrap_offcanvas_admin_bar_inline_styles();
		}

		wp_enqueue_script( 'jquery' );

		$js_version = $theme_version . '.' . filemtime( get_template_directory() . $theme_scripts );
		wp_enqueue_script( 'understrap-scripts', get_template_directory_uri() . $theme_scripts, array(), $js_version, true );
		understrap_nav_hover_clickable_parents_inline_script();
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
} // End of if function_exists( 'understrap_scripts' ).

add_action( 'wp_enqueue_scripts', 'understrap_scripts' );

if ( ! function_exists( 'understrap_nav_hover_inline_styles' ) ) {
	/**
	 * Open navbar dropdown menus on hover for desktop devices.
	 */
	function understrap_nav_hover_inline_styles(): void {
		$css = '
		.navbar .navbar-nav .dropdown-menu {
			border: 0;
		}

		@media ( min-width: 992px ) and ( hover: hover ) {
			.navbar .navbar-nav .dropdown:hover > .dropdown-menu,
			.navbar .navbar-nav .dropdown:focus-within > .dropdown-menu {
				display: block;
				margin-top: 0;
			}
		}';

		wp_add_inline_style( 'understrap-styles', $css );
	}
}

if ( ! function_exists( 'understrap_nav_hover_clickable_parents_inline_script' ) ) {
	/**
	 * Keep top-level dropdown parent links clickable on desktop.
	 */
	function understrap_nav_hover_clickable_parents_inline_script(): void {
		$script = '
		(function () {
			const mediaQuery = window.matchMedia("(min-width: 992px) and (hover: hover)");
			const selector = ".navbar .navbar-nav .dropdown > .dropdown-toggle";

			function updateDropdownToggles() {
				document.querySelectorAll(selector).forEach(function (link) {
					if (mediaQuery.matches) {
						link.removeAttribute("data-bs-toggle");
						link.removeAttribute("data-toggle");
					} else {
						link.setAttribute("data-bs-toggle", "dropdown");
						link.setAttribute("data-toggle", "dropdown");
					}
				});
			}

			updateDropdownToggles();

			if (typeof mediaQuery.addEventListener === "function") {
				mediaQuery.addEventListener("change", updateDropdownToggles);
			} else if (typeof mediaQuery.addListener === "function") {
				mediaQuery.addListener(updateDropdownToggles);
			}
		})();';

		wp_add_inline_script( 'understrap-scripts', $script, 'after' );
	}
}

if ( ! function_exists( 'understrap_offcanvas_admin_bar_inline_styles' ) ) {
	/**
	 * Add inline styles for the offcanvas component if the admin bar is visible.
	 *
	 * Fixes that the offcanvas close icon is hidden behind the admin bar.
	 *
	 * @since 1.2.0
	 */
	function understrap_offcanvas_admin_bar_inline_styles() {
		$navbar_type = get_theme_mod( 'understrap_navbar_type', 'collapse' );
		if ( 'offcanvas' !== $navbar_type ) {
			return;
		}

		$css = '
		body.admin-bar .offcanvas.show  {
			margin-top: 32px;
		}
		@media screen and ( max-width: 782px ) {
			body.admin-bar .offcanvas.show {
				margin-top: 46px;
			}
		}';
		wp_add_inline_style( 'understrap-styles', $css );
	}
}
