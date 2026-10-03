<?php
/**
 * SMJ Ulm – Nacht (Entwurf 2). Baut auf "SMJ Ulm" auf: Funktionen, Blöcke und
 * Inhaltsdarstellung kommen vom Eltern-Theme, hier stehen nur Gestaltung und Aufbau.
 *
 * @package SMJ_Ulm_Nacht
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'smj_nacht_assets', 20 );
add_action( 'after_setup_theme', 'smj_nacht_setup', 20 );

/**
 * Eigenes Stylesheet nach denen des Eltern-Themes, dazu das Skript für Kopfzeile und Einblendungen.
 */
function smj_nacht_assets() {
	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'smj-nacht', get_stylesheet_directory_uri() . '/assets/css/nacht.css', array( 'smj-theme', 'smj-inhalte' ), $version );
	wp_enqueue_script(
		'smj-nacht',
		get_stylesheet_directory_uri() . '/assets/js/nacht.js',
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
function smj_nacht_setup() {
	add_editor_style( 'assets/css/nacht.css' );
}
