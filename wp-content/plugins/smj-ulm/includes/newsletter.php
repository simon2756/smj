<?php
/**
 * Newsletter-Eintrag nach einer Anmeldung.
 *
 * Übernommen aus dem Code-Snippet "NewsletterAnmeldung" (Felix Betz, 02.03.2026):
 * Wird im Anmeldeformular beim Feld "Newsletter" "ja" gewählt, wird die Person mit Mail und
 * Name in Liste 1 des Newsletter-Plugins eingetragen, sofern sie noch nicht abonniert ist.
 * Das alte Snippet kann danach deaktiviert werden.
 *
 * @package SMJ_Ulm
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wpcf7_mail_sent', 'smj_ulm_newsletter_eintragen' );

/**
 * Trägt die Person in den Newsletter ein.
 */
function smj_ulm_newsletter_eintragen() {
	if ( ! class_exists( 'Newsletter' ) || ! class_exists( 'WPCF7_Submission' ) ) {
		return;
	}
	$submission = WPCF7_Submission::get_instance();
	if ( ! $submission ) {
		return;
	}

	// Feldnamen unabhängig von Groß-/Kleinschreibung auslesen.
	$data = array_change_key_case( $submission->get_posted_data(), CASE_LOWER );

	// CF7 liefert Checkbox-Werte als Array.
	$newsletter = $data['newsletter'] ?? '';
	$newsletter = is_array( $newsletter ) ? ( $newsletter[0] ?? '' ) : $newsletter;
	if ( 'ja' !== strtolower( trim( sanitize_text_field( $newsletter ) ) ) ) {
		return;
	}

	$email = sanitize_email( $data['email'] ?? $data['mail'] ?? '' );
	if ( ! is_email( $email ) ) {
		return;
	}

	$name = trim( sanitize_text_field( $data['vorname'] ?? '' ) . ' ' . sanitize_text_field( $data['nachname'] ?? '' ) );

	try {
		$newsletter_plugin = Newsletter::instance();
		if ( $newsletter_plugin->get_user( $email ) ) {
			return;
		}
		$newsletter_plugin->save_user(
			array(
				'email'  => $email,
				'name'   => $name,
				'status' => 'C',
				'list_1' => 1,
			)
		);
	} catch ( Exception $e ) {
		// Die Anmeldung selbst ist bereits verschickt; ein Fehler hier darf sie nicht stören.
		return;
	}
}
