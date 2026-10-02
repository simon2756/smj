<?php
/**
 * Anmeldeseite und Anmeldeschluss für Contact-Form-7-Formulare.
 *
 * - Auf der Seite einer Aktion wird das verknüpfte Formular automatisch angezeigt,
 *   nach dem Anmeldeschluss stattdessen ein Hinweis.
 * - Wird ein geschlossenes Formular irgendwo per Shortcode eingebunden, erscheint ebenfalls der Hinweis.
 * - Ein geschlossenes Formular nimmt serverseitig keine Anmeldung mehr an (auch nicht aus einem
 *   noch offenen Browser-Tab); dadurch speichert Advanced CF7 DB nichts und es wird keine Mail verschickt.
 *
 * @package SMJ_Ulm
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'the_content', 'smj_ulm_aktion_content', 20 );
add_filter( 'do_shortcode_tag', 'smj_ulm_shortcode_geschlossen', 10, 3 );
add_filter( 'wpcf7_spam', 'smj_ulm_submission_geschlossen', 1, 2 );
add_filter( 'wpcf7_display_message', 'smj_ulm_geschlossen_message', 10, 2 );

/**
 * Hinweistext für geschlossene Anmeldungen.
 *
 * @return string
 */
function smj_ulm_geschlossen_html() {
	return '<div class="smj-geschlossen" role="status"><strong>Die Anmeldung ist geschlossen.</strong> '
		. 'Die Aktion hat bereits begonnen oder ist vorbei. Bei Fragen meldet euch gerne bei uns.</div>';
}

/**
 * Baut die Anmeldeseite: Beschreibung + Formular, daneben die Eckdaten.
 *
 * @param string $content Inhalt.
 * @return string
 */
function smj_ulm_aktion_content( $content ) {
	if ( ! is_singular( 'smj_aktion' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$id       = get_the_ID();
	$formular = (int) get_post_meta( $id, '_smj_formular', true );
	$offen    = smj_ulm_ist_offen( $id );

	$form_html = '';
	if ( ! $offen ) {
		$form_html = smj_ulm_geschlossen_html();
	} elseif ( $formular && ! has_shortcode( $content, 'contact-form-7' ) && function_exists( 'wpcf7_contact_form_tag_func' ) ) {
		$form_html = '<div class="smj-formular">' . wpcf7_contact_form_tag_func( array( 'id' => (string) $formular ), null, 'contact-form-7' ) . '</div>';
	}

	$fakten = array(
		'Wann'   => smj_ulm_zeitraum( $id ),
		'Wo'     => get_post_meta( $id, '_smj_ort', true ),
		'Kosten' => get_post_meta( $id, '_smj_beitrag', true ),
	);
	$liste = '';
	foreach ( array_filter( $fakten ) as $label => $wert ) {
		$liste .= '<dt>' . esc_html( $label ) . '</dt><dd>' . esc_html( $wert ) . '</dd>';
	}

	$schluss = smj_ulm_anmeldeschluss( $id );
	$status  = $offen
		? ( $schluss ? 'Anmeldung offen bis ' . smj_ulm_datum( $schluss->modify( '-1 day' ), 'd.m.Y' ) : 'Anmeldung offen' )
		: 'Anmeldung geschlossen';

	$loesch = smj_ulm_loeschdatum( $id );
	$info   = '<aside class="smj-aktion__info">'
		. '<div class="smj-box">'
		. '<p class="smj-status ' . ( $offen ? 'is-offen' : 'is-geschlossen' ) . '">' . esc_html( $status ) . '</p>'
		. ( $liste ? '<dl class="smj-fakten">' . $liste . '</dl>' : '' )
		. '</div>'
		. ( $offen ? '<div class="smj-box smj-box--hinweis">'
		. '<h2>So geht es weiter</h2>'
		. '<ol><li>Nach dem Absenden kommt sofort eine Bestätigung per E-Mail.</li>'
		. '<li>Mit der Überweisung des Teilnahmebeitrags ist die Anmeldung wirksam.</li>'
		. '<li>Ein paar Tage vorher kommt eine kurze Packliste.</li></ol>'
		. ( $loesch ? '<p class="smj-datenschutz">Alle Angaben werden <strong>' . intdiv( SMJ_ULM_LOESCHFRIST_TAGE, 7 ) . ' Wochen nach der Aktion automatisch gelöscht</strong> (am ' . esc_html( smj_ulm_datum( $loesch, 'd.m.Y' ) ) . ').</p>' : '' )
		. '</div>' : '' )
		. '</aside>';

	return '<div class="smj-aktion"><div class="smj-aktion__main">' . $content . $form_html . '</div>' . $info . '</div>';
}

/**
 * Findet das Formular zu den Attributen eines CF7-Shortcodes.
 *
 * @param array $attr Shortcode-Attribute.
 * @return int Post-ID oder 0.
 */
function smj_ulm_formular_aus_shortcode( $attr ) {
	if ( ! function_exists( 'wpcf7_contact_form' ) ) {
		return 0;
	}
	$id    = isset( $attr['id'] ) ? trim( (string) $attr['id'] ) : '';
	$title = isset( $attr['title'] ) ? trim( (string) $attr['title'] ) : '';

	$form = $id ? wpcf7_get_contact_form_by_hash( $id ) : null;
	if ( ! $form && $id ) {
		$form = wpcf7_contact_form( $id );
	}
	if ( ! $form && $title ) {
		$form = wpcf7_get_contact_form_by_title( $title );
	}
	return $form ? (int) $form->id() : 0;
}

/**
 * Ersetzt ein geschlossenes Formular im Shortcode durch den Hinweis.
 *
 * @param string       $output Ausgabe.
 * @param string       $tag    Shortcode.
 * @param array|string $attr   Attribute.
 * @return string
 */
function smj_ulm_shortcode_geschlossen( $output, $tag, $attr ) {
	if ( 'contact-form-7' !== $tag || ! is_array( $attr ) ) {
		return $output;
	}
	$form_id = smj_ulm_formular_aus_shortcode( $attr );
	return $form_id && smj_ulm_formular_geschlossen( $form_id ) ? smj_ulm_geschlossen_html() : $output;
}

/**
 * Lehnt Anmeldungen an geschlossene Formulare serverseitig ab.
 *
 * Läuft über den Spam-Filter, weil CF7 eine als Spam markierte Einsendung weder speichert
 * noch per Mail verschickt.
 *
 * @param bool              $spam       Bisheriges Ergebnis.
 * @param WPCF7_Submission  $submission Einsendung.
 * @return bool
 */
function smj_ulm_submission_geschlossen( $spam, $submission ) {
	$form = $submission->get_contact_form();
	if ( $form && smj_ulm_formular_geschlossen( (int) $form->id() ) ) {
		$GLOBALS['smj_ulm_abgelehnt'] = true;
		return true;
	}
	return $spam;
}

/**
 * Zeigt bei abgelehnter Anmeldung den passenden Text statt der Spam-Meldung.
 *
 * @param string $message Meldung.
 * @param string $status  Meldungstyp.
 * @return string
 */
function smj_ulm_geschlossen_message( $message, $status ) {
	if ( 'spam' === $status && ! empty( $GLOBALS['smj_ulm_abgelehnt'] ) ) {
		return 'Die Anmeldung ist leider geschlossen.';
	}
	return $message;
}
