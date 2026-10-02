<?php
/**
 * SMJ Ulm – Theme-Funktionen.
 *
 * Funktionen der Website (Anmeldungen, Termine, Newsletter) stecken im Plugin
 * "SMJ Ulm – Funktionen". Das Theme kümmert sich nur um Darstellung und die
 * Gestaltungsblöcke Diashow, Countdown und Brotkrümel.
 *
 * @package SMJ_Ulm_Theme
 */

defined( 'ABSPATH' ) || exit;

define( 'SMJ_THEME_VERSION', wp_get_theme( get_template() )->get( 'Version' ) );

require_once get_theme_file_path( 'inc/bloecke.php' );

add_action( 'after_setup_theme', 'smj_theme_setup' );
add_action( 'wp_enqueue_scripts', 'smj_theme_assets' );

/**
 * Theme-Unterstützung.
 */
function smj_theme_setup() {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/theme.css' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo' );
}

/**
 * Stylesheet und Skript für die Website.
 */
function smj_theme_assets() {
	wp_enqueue_style( 'smj-theme', get_theme_file_uri( 'assets/css/theme.css' ), array(), SMJ_THEME_VERSION );
}

add_action( 'after_setup_theme', 'smj_theme_menus' );

/**
 * Menü-Positionen mit denselben Namen wie im alten Theme, damit WordPress die
 * bestehenden Menüs beim Theme-Wechsel automatisch übernimmt.
 */
function smj_theme_menus() {
	register_nav_menus(
		array(
			'primary'     => 'Hauptmenü',
			'footer-menu' => 'Footer-Menü',
		)
	);
}

add_filter( 'render_block_data', 'smj_theme_navigation_aus_menue' );
add_filter( 'render_block_core/shortcode', 'smj_theme_shortcode_in_parts' );

/**
 * Füllt die Navigationsblöcke im Kopf- und Fußbereich mit dem klassischen Menü, das der
 * Menü-Position zugewiesen ist. So bleiben die Menüs unter Design → Menüs pflegbar,
 * auch ohne das Gutenberg-Plugin. Wird im Website-Editor ein eigenes Navigationsmenü
 * gewählt (Attribut "ref"), hat dieses Vorrang.
 *
 * @param array $block Geparster Block.
 * @return array
 */
function smj_theme_navigation_aus_menue( $block ) {
	if (
		'core/navigation' !== $block['blockName'] ||
		empty( $block['attrs']['__unstableLocation'] ) ||
		! empty( $block['attrs']['ref'] ) ||
		! empty( $block['innerBlocks'] )
	) {
		return $block;
	}

	$locations = get_nav_menu_locations();
	$menu_id   = $locations[ $block['attrs']['__unstableLocation'] ] ?? 0;
	$menu      = $menu_id ? wp_get_nav_menu_object( $menu_id ) : false;
	if ( ! $menu || ! class_exists( 'WP_Classic_To_Block_Menu_Converter' ) ) {
		return $block;
	}

	$markup = WP_Classic_To_Block_Menu_Converter::convert( $menu );
	if ( ! is_string( $markup ) || '' === $markup ) {
		return $block;
	}

	$inner = array_values( array_filter( parse_blocks( $markup ), static fn( $b ) => ! empty( $b['blockName'] ) ) );
	$block['innerBlocks']  = $inner;
	$block['innerContent'] = array_fill( 0, count( $inner ), null );
	return $block;
}

/**
 * WordPress führt Shortcodes nur im Template selbst aus, nicht in Template-Teilen wie dem
 * Fußbereich. Damit z. B. das Newsletter-Formular dort funktioniert, wird der Shortcode-Block
 * hier ausgeführt.
 *
 * @param string $content Inhalt des Shortcode-Blocks.
 * @return string
 */
function smj_theme_shortcode_in_parts( $content ) {
	return do_shortcode( $content );
}
