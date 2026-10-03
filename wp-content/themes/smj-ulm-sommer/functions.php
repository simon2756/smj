<?php
/**
 * SMJ Ulm – Sommer (Entwurf 3). Baut auf "SMJ Ulm" auf: Funktionen, Blöcke und
 * Inhaltsdarstellung kommen vom Eltern-Theme, hier stehen nur Gestaltung und Aufbau.
 *
 * @package SMJ_Ulm_Sommer
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'smj_sommer_assets', 20 );
add_action( 'after_setup_theme', 'smj_sommer_setup', 20 );

/**
 * Eigenes Stylesheet nach denen des Eltern-Themes, dazu das Skript für das Termin-Laufband.
 */
function smj_sommer_assets() {
	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'smj-sommer', get_stylesheet_directory_uri() . '/assets/css/sommer.css', array( 'smj-theme', 'smj-inhalte' ), $version );
	wp_enqueue_script(
		'smj-sommer',
		get_stylesheet_directory_uri() . '/assets/js/sommer.js',
		array(),
		$version,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}

/**
 * Editor zeigt die Gestaltung dieses Entwurfs.
 */
function smj_sommer_setup() {
	add_editor_style( 'assets/css/sommer.css' );
}
