<?php
/**
 * Plugin Name:       SMJ Ulm – Funktionen
 * Description:       Anmeldungen mit automatischem Anmeldeschluss und Datenlöschung, Terminliste aus dem SMJ-Kalender, Newsletter-Eintrag nach Anmeldung.
 * Version:           1.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Author:            SMJ Ulm/Alb/Donau
 * License:           GPL-2.0-or-later
 * Text Domain:       smj-ulm
 *
 * @package SMJ_Ulm
 */

defined( 'ABSPATH' ) || exit;

define( 'SMJ_ULM_VERSION', '1.1.0' );
define( 'SMJ_ULM_FILE', __FILE__ );
define( 'SMJ_ULM_DIR', plugin_dir_path( __FILE__ ) );
define( 'SMJ_ULM_URL', plugin_dir_url( __FILE__ ) );

/** Tage nach Aktionsbeginn, nach denen alle Anmeldedaten gelöscht werden. */
define( 'SMJ_ULM_LOESCHFRIST_TAGE', 21 );

require_once SMJ_ULM_DIR . 'includes/aktionen.php';
require_once SMJ_ULM_DIR . 'includes/admin.php';
require_once SMJ_ULM_DIR . 'includes/formular.php';
require_once SMJ_ULM_DIR . 'includes/loeschung.php';
require_once SMJ_ULM_DIR . 'includes/newsletter.php';
require_once SMJ_ULM_DIR . 'includes/termine.php';
require_once SMJ_ULM_DIR . 'includes/bloecke.php';
require_once SMJ_ULM_DIR . 'includes/uebernahme.php';

register_activation_hook( __FILE__, 'smj_ulm_activate' );
register_deactivation_hook( __FILE__, 'smj_ulm_deactivate' );

/**
 * Registriert den Inhaltstyp (für die Permalinks) und den täglichen Lösch-Job.
 */
function smj_ulm_activate() {
	smj_ulm_register_aktion();
	flush_rewrite_rules();
	smj_ulm_schedule_cleanup();
}

/**
 * Entfernt den Lösch-Job. Gespeicherte Daten bleiben unangetastet.
 */
function smj_ulm_deactivate() {
	wp_clear_scheduled_hook( 'smj_ulm_cleanup' );
	flush_rewrite_rules();
}
